<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Part;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Manufacture;
use App\Models\User;
use App\Models\Product_plan;
use App\Models\Linkman;
use App\Models\Quotation;
use App\Models\Delivery_note;
use App\Models\Delivery;
use PDF;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Contracts\Encryption\DecryptException;

class DepotController extends Controller
{
    //
	public function PartList(Request $request)
    {
		//DB::enableQueryLog();
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('orde') && $search['orde'] = $request->get('orde');
			$request->get('edat') && $search['edat'] = $request->get('edat');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		
		$parts = Part::where(function($query) use (&$search){
						if(array_has($search, 'stat'))
							$query->where('parts.status',$search['stat']);
						else
							$query->where('parts.status', '>=', 2);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'orde':
									$query->where('orders.no', 'like', '%'.$val.'%');break;
								case 'edat':
									$query->whereDate('parts.due_date', '<=' , $val);break;
							}
						}
					})
				->leftJoin('orders', function ($join) {
						$join->on('orders.id', '=', 'parts.order_id')
						->whereNull ('orders.deleted_at')
						->where('orders.tenat_id', $this->GetLoginUser()->tenant_id);
					})
				->join('customers', 'customers.id', '=', 'orders.customer_id')
				->select('parts.id','parts.name','parts.diagram','parts.volume_size','parts.product_num','parts.due_date','parts.status','orders.no as orderno', 'customers.name as cusname')
				->selectRaw('(select sum(yscount) from deliverys where parts.id=part_id) as yscount')
				->orderBy('parts.id', 'desc')
				->paginate(5);
		$parts->appends($search)->links();
		return $this->response->array($parts->toArray());
    }
	//添加送货单
	public function AddDepotnote(Request $request)
    {
		$this->validate($request, ['parts' => 'required']);
		$parttmp = $request->parts;
		if(!empty($request->submit))
			$parttmp =array_pluck($request->parts, 'id');
		$cusids = Part::whereIn('parts.id', $parttmp)
					->join('orders', 'orders.id', '=', 'parts.order_id')
					->pluck('orders.customer_id');
		if(count(array_count_values($cusids->toArray())) != 1)return $this->response->errorUnauthorized('同一客户的零件才能合并送货');
		
		$parts = Part::whereIn('parts.id', $parttmp)
				->join('orders', 'orders.id', '=', 'parts.order_id')
				->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
				->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
				->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
				->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
				->select('parts.id','parts.order_id','parts.name','parts.volume_size','parts.product_num','parts.diagram','parts.remark','parts.requirements','parts.start_date','parts.due_date','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'orders.no as orderno')
				->selectRaw('(select sum(yscount) from deliverys where parts.id=part_id) as yscounta')
				->get();
		
		//同订单的零件才能合并送货
		if(count(array_count_values($parts->pluck('orderno')->toArray())) != 1)return $this->response->errorUnauthorized('同订单的零件才能合并送货');
		
		$order = Order::find($parts->pluck('order_id')->first());
		if(empty($order))return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'订单已删除或无效'));
		
		
		//$manuno = date("Ymd").'-'.Customer::where('id', $cusids[0])->value('no').'-xxxx-xx';
		
		$lxy = explode(",",$order->lkmans);
		
		$qt = Quotation::where('order_id', $order->id)->select('no','created_at')->first();
		
		$out = array();
		$out['no'] = $qt->no;
		$out['orderno'] = $order->no;
		$out['customer_id'] = $cusids[0];
		$out['created_at'] = $qt->created_at->toDateString();
		$out['qrcode_url'] = null;
		$out['lkmans'] = Linkman::whereIn('id',$lxy)->get()->toArray();
		$out['postaddr'] = Linkman::find($order->postaddr);
		$out['invoaddr'] = Linkman::find($order->invoaddr);
		$out['ticket_info'] = unserialize($order->ticket_info);
		$out['cusname'] = Customer::where('id', $cusids[0])->value('name');
		$out['savefax'] = Customer::where('id', $cusids[0])->value('savefax');
		$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
		$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
		$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
		$out['parts'] = $parts->toArray();
		if(empty($request->submit)){
			return $this->response->array($out);
		}else{
			foreach($out['parts'] as $key => $item){
				foreach($request->parts as $i){
					if($item['id'] == $i['id']){
						if(($i['yscount'] <= ((int)$item['product_num']-(int)$item['yscounta'])) and ($i['yscount'] > 0)){
							$istat = ($i['yscount'] == ((int)$item['product_num']-(int)$item['yscounta']))? 4 : 3;
							Part::where('id', $item['id'])->update(['status' => $istat]);
							$out['parts'][$key]['yscount'] = $i['yscount'];break;
						}else{
							return $this->response->array(array('code'=>100,'error'=>'error','msg'=>$item['name'].'送货数量有误'));
						}
					}
				}
			}
			
			$this->validate($request, [
				'parts' => 'required',
				'orderno' => 'required',
				'customer_id' => 'required',
				'dismod' => 'required',
			]);
			
			$partids = array_pluck($request->parts, 'id');
			
			$rnd = date("YmdHis").rand(1111,9999);
			$rot = 'delivery/'.$rnd.'.pdf';
			$den = new Delivery_note;
			$den->no = $rnd;
			$den->orderno = $request->orderno;
			$den->distribution_mode = $request->dismod;
			$den->parts = json_encode($partids, JSON_NUMERIC_CHECK);
			$request->express_name && $den->express_name = $request->express_name;
			$request->express_code && $den->express_code = $request->express_code;
			$request->express_no && $den->express_no = $request->express_no;
			if(!empty($request->express_code)){
				Customer::where('id', $request->customer_id)->update(['savefax' => $request->express_code]);
			}
			$den->customer_id = $request->customer_id;
			$den->disk = $rot;
			$den->status =$request->status ? $request->status : 0;
			if(!file_exists(public_path('delivery')))
				mkdir(public_path('delivery'));
			$filename = $rnd.'.png';
			$qurul = env('APP_URL').'/delivery/'.$filename;
			$den->qrcodeurl = $qurul;
			$den->save();
			
			$url = url('/wechat/order/logistics').'?val='.encrypt($order->id);
			QrCode::format('png')->size(488)->generate($url,public_path('delivery/'.$filename));
			
			foreach($request->parts as $item){
				$dev = new Delivery([
					'part_id' => $item['id'],
					'delivery_note_id' => $den->id,
					'yscount' => $item['yscount']
				]);
				$dev->save();
			}
			
			$partstatus = Part::whereIn('id',json_decode($order->parts, true))->select('status')->get();
			if($order->status < 5){
				$order->status = 5;
				$order->save();
			}
			if($partstatus->avg('status') == 4){
				$this->SendWechatMessage(array('utype'=>'customer','uid'=>$request->customer_id),1,5,$request->orderno,$url);
			}else{
				$this->SendWechatMessage(array('utype'=>'customer','uid'=>$request->customer_id),1,9,$request->orderno,$url);
			}
			$url = env('APP_URL').'/admin/#/modules/index';
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->manger),1,8,$request->orderno,$url);
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->pojer),1,8,$request->orderno,$url);
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->saler),1,8,$request->orderno,$url);
			
			$out['qrcode_url'] = $qurul;
			$out['dismod'] = $request->dismod;
			$out['express_name'] = empty($request->express_name) ? '' : $request->express_name;
			$out['express_code'] = empty($request->express_code) ? '' : $request->express_code;
			$out['express_no'] = empty($request->express_no) ? '' : $request->express_no;
			if (View::exists('delivery_note.template')) {
				$view = View::make('delivery_note.template')->with('data',$out);
				$html = response($view)->getContent();
				//return $this->response->array($html);
				//PDF::loadHTML($html)->setPaper('a4')->save('eliverynote.pdf');
				PDF::loadHTML($html)->setPaper('a4')->setWarnings(false)->save($rot);
				//return $this->response->array($html);
			}

			return $this->response->array(array('code'=>200,'success'=>'ok'));
		}
		
	}
	//获取送货单
	public function GetDepotnote($id)
    {
		
		$deno = Delivery_note::find($id);
		$parts = Part::whereIn('parts.id', json_decode($deno->parts,true))
				->join('orders', 'orders.id', '=', 'parts.order_id')
				->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
				->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
				->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
				->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
				->select('parts.id','parts.order_id','parts.name','parts.volume_size','parts.product_num','parts.diagram','parts.remark','parts.requirements','parts.start_date','parts.due_date','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'orders.no as orderno')
				->selectRaw('(select sum(yscount) from deliverys where parts.id=part_id) as yscount')
				->get();
		$order = Order::where('no',$deno->orderno)->first();
		if(empty($order))return $this->response->errorUnauthorized('订单已删除或无效');
		
		$lxy = explode(",",$order->lkmans);
		
		$qt = Quotation::where('order_id', $order->id)->select('no','created_at')->first();
		
		$out = array();
		$out['no'] = $qt->no;
		$out['orderno'] = $order->no;
		$out['created_at'] = $qt->created_at->toDateString();
		$out['qrcode_url'] = $deno->qrcodeurl;
		$out['lkmans'] = Linkman::whereIn('id',$lxy)->select('linkman_name','lk_phone','province','city','area','lk_address')->get()->toArray();
		$out['postaddr'] = Linkman::find($order->postaddr);
		$out['invoaddr'] = Linkman::find($order->invoaddr);
		$out['ticket_info'] = unserialize($order->ticket_info);
		$out['cusname'] = Customer::where('id', $order->customer_id)->value('name');
		$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
		$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
		$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
		$out['parts'] = $parts->toArray();
		$out['dismod'] = $deno->distribution_mode;
		switch($deno->distribution_mode){
			case 0:
				$out['express_name'] = $deno->express_name;
				$out['express_no'] = $deno->express_no;
				break;
			case 1:
				$out['shlkman'] = Linkman::where('id',$deno->shlkuser)->select('linkman_name','lk_phone','province','city','area','lk_address')->first();
				break;
			case 2:
				$out['ztlkman'] = User::whereIn('id',$deno->ztlkuser)->select('name','phone')->first();
				break;
		}
		if (View::exists('delivery_note_html.template')) {
				$view = View::make('delivery_note.template')->with('data',$out);
				$html = response($view)->getContent();
				return $this->response->array($html);
		}
		return $this->response->array($out);

		
	}
	//送货单列表
	public function DepotnoteList(Request $request)
    {
		//DB::enableQueryLog();
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('orde') && $search['orde'] = $request->get('orde');
			$request->get('edat') && $search['edat'] = $request->get('edat');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
			$request->get('expo') && $search['expo'] = $request->get('expo');
		
		$den = Delivery_note::where(function($query) use (&$search){
						if(array_has($search, 'stat'))
							$query->where('delivery_notes.status',$search['stat']);
						else
							$query->where('delivery_notes.status', '>=', 0);
						$query->where('customers.tenat_id', $this->GetLoginUser()->tenant_id);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'orde':
									$query->where('delivery_notes.orderno', $val);break;
								case 'edat':
									$query->whereDate('delivery_notes.created_at', '<=' , $val);break;
								case 'expo':
									$query->where('delivery_notes.express_no', $val);break;
							}
						}
					})
				->join('customers', 'customers.id', '=', 'delivery_notes.customer_id')
				->select('delivery_notes.*','customers.name as cusname')
				->orderBy('delivery_notes.id', 'desc')
				->paginate(5);
		$den->appends($search)->links();
		return $this->response->array($den->toArray());
    }
	//设置送货单完成
	public function DepotnoteSetok(Request $request)
    {
		$this->validate($request, ['depos' => 'required']);
		Delivery_note::whereIn('id',$request->depos)->update(['status' => 2]);
		return $this->response->array(['message'=>'ok']);
    }
	
	//客户确认收货接口
	public function CustomerDepotnoteSetok(Request $request)
    {
		$this->validate($request, ['val' => 'required']);
		$oid = urldecode($request->Input('val'));
		
		try {
			$oid = decrypt($oid);
		} catch (DecryptException $e) {
			return $this->response->errorUnauthorized('错误');
		}
		$deno = Delivery_note::find($oid);
		//return $this->response->array(['message'=>decrypt($request->val)]);
		if(empty($deno))return $this->response->errorUnauthorized('错误：找不到此送货单');
		$redeiurl = '';
		$url =  env('APP_URL').'/customer/orderlist';
		$order = Order::where('no',$deno->orderno)->first();
		$partstatus = Part::whereIn('id',json_decode($order->parts, true))->select('status')->get();
		if($partstatus->avg('status') == 4){
			if($order->status < 6){
				$order->status = 6;
				$order->save();
				$url2 = env('APP_URL').'/admin/#/modules/index';
				$redeiurl = env('APP_URL').'/wechat/order/evaluate?val='.encrypt($order->id);
				$this->SendWechatMessage(array('utype'=>'customer','uid'=>$order->customer_id),1,12,$order->no,$redeiurl);
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->manger),1,10,$order->no,$url2);
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->pojer),1,10,$order->no,$url2);
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->saler),1,10,$order->no,$url2);
			}
		}else{
			$this->SendWechatMessage(array('utype'=>'customer','uid'=>$order->customer_id),1,13,$order->no,$url);
			$url2 = env('APP_URL').'/admin/#/modules/index';
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->manger),1,11,$order->no,$url2);
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->pojer),1,11,$order->no,$url2);
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->saler),1,11,$order->no,$url2);
		}
		
		$deno->status = 1;
		$deno->save();
		return $this->response->array(['message'=>'已确认收货','redeiurl'=>$redeiurl]);
    }
	//超过7天自动收货接口
	public function AutoDepotnoteSetok(Request $request)
    {
		$date = date('Y-m-d', strtotime('-7 days'));
		//$tete= Delivery_note::where('status',0)->whereDate('created_at','<',$date)->get();
		//return $this->response->array($tete->toArray());
		Delivery_note::where('status',0)->whereDate('created_at','<',$date)->chunk(200, function ($delns) {
			foreach ($delns as $deno) {
				$url =  env('APP_URL').'/customer/orderlist';
				$order = Order::find($deno->id);
				if(!empty($order)){
					$partstatus = Part::whereIn('id',json_decode($order->parts, true))->select('status')->get();
					if($partstatus->avg('status') == 4){
						if($order->status < 6){
							$order->status = 6;
							$order->save();
							$url2 = env('APP_URL').'/admin/#/modules/index';
							$this->SendWechatMessage(array('utype'=>'customer','uid'=>$order->customer_id),1,18,$order->no,env('APP_URL').'/wechat/order/evaluate?val='.encrypt($order->id));
							$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->manger),1,16,$order->no,$url2);
							$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->pojer),1,16,$order->no,$url2);
							$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->saler),1,16,$order->no,$url2);
						}
					}else{
						$this->SendWechatMessage(array('utype'=>'customer','uid'=>$order->customer_id),1,19,$order->no,$url);
						$url2 = env('APP_URL').'/admin/#/modules/index';
						$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->manger),1,17,$order->no,$url2);
						$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->pojer),1,17,$order->no,$url2);
						$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->saler),1,17,$order->no,$url2);
					}
					$te = Delivery_note::find($deno->id);
					$te->status = 2;
					$te->save();
				}
			}
		});
		return $this->response->array(['message'=>'ok']);
    }
}
