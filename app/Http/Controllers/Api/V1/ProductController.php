<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Part;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Manufacture;
use App\Models\User;
use App\Models\Product_plan;
use Overtrue\Pinyin\Pinyin;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    //
	public function PartList(Request $request)
    {
		//DB::enableQueryLog();
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('mate') && $search['mate'] = $request->get('mate');
			$request->get('mold') && $search['mold'] = $request->get('mold');
			$request->get('surf') && $search['surf'] = $request->get('surf');
			$request->get('equi') && $search['equi'] = $request->get('equi');
			$request->get('edat') && $search['edat'] = $request->get('edat');
		
		$parts = Part::where(function($query) use (&$search){
						$query->where('parts.status', 0);
						$query->where('orders.tenat_id', $this->GetLoginUser()->tenant_id);
						$query->where('orders.status', '>=' ,2);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'mate':
									$query->where('materials.id', '=', $val);break;
								case 'mold':
									$query->where('molding_processes.id', '=', $val);break;
								case 'surf':
									$query->where('surfaces.id', '=', $val);break;
								case 'equi':
									$query->where('equipments.id', '=', $val);break;
								case 'edat':
									$query->whereDate('parts.due_date', '<=' , $val);break;
							}
						}
					})
				->leftJoin('orders', function ($join) {
						$join->on('orders.id', '=', 'parts.order_id')
						->whereNull ('orders.deleted_at');
					})
				->join('customers', 'customers.id', '=', 'orders.customer_id')
				->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
				->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
				->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
				->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
				->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'orders.no as orderno', 'customers.name as cusname')
				->orderBy('parts.id', 'desc')
				->paginate(5);
		$parts->appends($search)->links();
		//return $this->response->array(DB::getQueryLog());
		return $this->response->array($parts->toArray());
		//return $this->response->array(DB::getQueryLog());
		//return $this->response->array($search);
    }
	//添加生产任务书-废弃
	public function AddManufacture2(Request $request)
    {
		$this->validate($request, ['parts' => 'required']);
		$cusids = Part::whereIn('parts.id', $request->parts)
					->join('orders', 'orders.id', '=', 'parts.order_id')
					->pluck('orders.customer_id');
		Part::whereIn('id', $request->parts)->update(['status'=>1]);
		if(count(array_count_values($cusids->toArray())) != 1)return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'同一客户的零件才能合并生产'));

		$manuno = date("Ymd").'-'.Customer::where('id', $cusids[0])->value('no').'-xxxx-xx';
		$manufacture = new Manufacture;
		$manufacture->prouse_id = 0;
		$manufacture->no = $manuno;
		$manufacture->parts = json_encode($request->parts, JSON_NUMERIC_CHECK);
		$manufacture->tenat_id = 0;
		$manufacture->customer_id = $cusids[0];
		$manufacture->qrcode_url = 0;
		$manufacture->status =$request->status ? $request->status : 0;
		$manufacture->save();
		return $this->response->array(array('code'=>200,'success'=>'ok','id'=>$manufacture->id));

	}
	//添加生产任务书
	public function AddManufacture(Request $request)
    {
		if(empty($request->submit)){
			$this->validate($request, ['parts' => 'required']);
			$cusids = Part::whereIn('parts.id', $request->parts)
						->join('orders', 'orders.id', '=', 'parts.order_id')
						->pluck('orders.customer_id');

			if(count(array_count_values($cusids->toArray())) != 1)return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'同一客户的零件才能合并生产'));
			
			$parts = Part::whereIn('parts.id', $request->parts)
					->join('orders', 'orders.id', '=', 'parts.order_id')
					->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
					->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
					->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
					->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
					->select('parts.id','parts.order_id','parts.name','parts.volume_size','parts.product_num','parts.diagram','parts.remark','parts.start_date','parts.due_date','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'orders.no as orderno')
					->get();
			
			$order = Order::find($parts->pluck('order_id')->first());
			if(empty($order))return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'订单已删除或无效'));
			
			$pinyin = new Pinyin();
			$jc = $pinyin->abbr($this->GetLoginUser()->name, PINYIN_KEEP_ENGLISH);
			$manuno = date("ymdHis").'-'.Customer::where('id',$cusids[0])->value('no').'-'.Tenant::where('id', $this->GetLoginUser()->tenant_id)->value('daihao').'-'.$jc;
			
			$out = array();
			$out['no'] = $manuno;
			$out['created_at'] = date('Y-m-d');
			$out['customer_id'] = $cusids[0];
			$out['qrcode_url'] = null;
			$out['cusname'] = Customer::where('id', $cusids[0])->value('name');
			$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
			$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
			$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
			$out['parts'] = $parts->toArray();
			
			return $this->response->array($out);
		}else{
			$this->validate($request, [
				'parts' => 'required',
				'no' => 'required',
				'customer_id' => 'required',
				'prouid' => 'required|numeric',
			]);
			$parts = $request->parts;
			$pids = array();
			foreach($parts as $item)
				$pids[] = (int)$item['id'];
			Part::whereIn('id', $pids)->update(['status'=>1]);
			$manufacture = new Manufacture;
			$manufacture->prouse_id = $request->prouid;
			$manufacture->no = $request->no;
			$manufacture->parts = json_encode($pids, JSON_NUMERIC_CHECK);
			$manufacture->tenat_id = $this->GetLoginUser()->tenant_id;
			$manufacture->customer_id = $request->customer_id;
			if(!file_exists(public_path('manufacture')))
				mkdir(public_path('manufacture'));
			$filename = date("YmdHis").rand(1111,9999).'.png';
			$qurul = env('APP_URL').'/manufacture/'.$filename;
			$manufacture->qrcode_url = $qurul;
			$manufacture->status =$request->status ? $request->status : 0;
			$manufacture->save();
			QrCode::format('png')->size(488)->generate('https://yp-dev.one2fit.cn/manufacture?val='.encrypt($manufacture->id),public_path('manufacture/'.$filename));
			$url = 'https://yp-dev.one2fit.cn/admin/#/modules/index';
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$request->prouid),1,6,'',$url);
			
			foreach($parts as $item){
				if(!empty($item['reqmen']))
					Part::where('id', $item['id'])->update(['requirements' => $item['reqmen']]);
				$pp = new Product_plan([
						'manufacture_id' => $manufacture->id,
						'jh_data'=>empty($item['jhtime']) ? null : $item['jhtime'],
						'part_id'=>$item['id'],
					]);
				$pp->save();
			}
			return $this->response->array(array('code'=>200,'success'=>'ok'));
		}

	}
	//查看生产任务书
	public function ChackManufacture($id){
			$manufacture = Manufacture::where('manufactures.id', $id)
							->where('manufactures.tenat_id',$this->GetLoginUser()->tenant_id)
							->leftJoin('customers', 'customers.id', '=', 'manufactures.customer_id')
							->select('manufactures.*','customers.name as cusname')
							->first();

			if(empty($manufacture))return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'无效生产任务书'));
			$partids = json_decode($manufacture->parts,true);
			$orderid = Part::where('id',$partids[0])->value('order_id');
			$parts = Part::whereIn('parts.id', $partids)
					->join('orders', 'orders.id', '=', 'parts.order_id')
					->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
					->leftJoin('product_plans','product_plans.part_id', '=', 'parts.id')
					->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
					->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
					->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
					->select('parts.id','parts.order_id','parts.name','parts.volume_size','parts.requirements','parts.product_num','parts.diagram','parts.remark','parts.start_date','parts.due_date','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'orders.no as orderno', 'product_plans.jh_data')
					->get();
			
			$order = Order::find($orderid);
			if(empty($order))return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'订单已删除或无效'));

			$out = $manufacture->toArray();
			$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
			$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
			$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
			$out['prouse_id'] = User::where('id', $manufacture->prouse_id)->select('name','phone')->first();
			$out['parts'] = $parts->toArray();
			
			return $this->response->array($out);
	}
	//获取生产任务书
	public function GetManufacture($id,Request $request)
    {
		if(empty($request->submit)){
			$manufacture = Manufacture::where('manufactures.id', $id)
							->where('manufactures.tenat_id',$this->GetLoginUser()->tenant_id)
							->leftJoin('customers', 'customers.id', '=', 'manufactures.customer_id')
							->select('manufactures.*','customers.name as cusname')
							->first();
			if(empty($manufacture))return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'无效生产任务书'));
			
			$parts = Part::whereIn('parts.id', json_decode($manufacture->parts,true))
							->join('orders', 'orders.id', '=', 'parts.order_id')
							->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
							->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
							->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
							->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
							->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'orders.no as orderno')
							->get();
			
			$order = Order::find($parts->pluck('order_id')->first());
			if(empty($order))return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'订单已删除或无效'));
			
			$out = $manufacture->toArray();
			$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
			$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
			$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
			$out['parts'] = $parts->toArray();

			return $this->response->array($out);
		}else{
			$this->validate($request, [
				'prouid' => 'required|numeric',
				'parts' => 'required'
			]);

			Manufacture::where('id', $id)->update(['prouse_id' => $request->prouid]);
			$parts = $request->parts;
			foreach($parts as $item){
				if(!empty($item['reqmen']))
					Part::where('id', $item['id'])->update(['requirements' => $item['reqmen']]);
				$pp = new Product_plan([
						'manufacture_id' => $id ,
						'jh_data'=>isset($item['jhtime']) ? null : $item['jhtime'],
						'part_id'=>$item['id'],
					]);
				$pp->save();
			}
			return $this->response->array(array('code'=>200,'success'=>'ok'));
		}

	}
	//获取生产任务书列表
	public function GetManufactures(Request $request)
    {
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('mate') && $search['mate'] = $request->get('mate');
			$request->get('mold') && $search['mold'] = $request->get('mold');
			$request->get('surf') && $search['surf'] = $request->get('surf');
			$request->get('equi') && $search['equi'] = $request->get('equi');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
			
		$list = Manufacture::where(function($query) use (&$search){
						$query->where('manufactures.tenat_id', $this->GetLoginUser()->tenant_id);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'mate':
									$query->where('materials.id', '=', $val);break;
								case 'mold':
									$query->where('molding_processes.id', '=', $val);break;
								case 'surf':
									$query->where('surfaces.id', '=', $val);break;
								case 'equi':
									$query->where('equipments.id', '=', $val);break;
								case 'stat':
									$query->where('manufactures.status', '=' , $val);break;
								case 'jhdt':
									break;
							}
						}
					})
					->join('customers', 'customers.id', '=', 'manufactures.customer_id')
					->select('manufactures.*','customers.name as cusname')
					->orderBy('manufactures.id', 'desc')
					->paginate(5);
		$list->appends($search)->links();
		$tmp = $list->toArray();
		foreach($tmp['data'] as $key => $val){
			$parts = Part::whereIn('parts.id',  json_decode($val['parts'],true))
							->join('orders', 'orders.id', '=', 'parts.order_id')
							->join('product_plans', 'product_plans.part_id', '=', 'parts.id')
							->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
							->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
							->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
							->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
							->select('parts.name','parts.volume_size','parts.product_num','parts.diagram','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'orders.no as orderno','product_plans.jh_data as jhdata','product_plans.start_data as plstatime','product_plans.end_data as plendtime','product_plans.status as plstat')
							->get();
			$tmp['data'][$key]['parts'] = $parts->toArray();
		}
		return $this->response->array($tmp);

	}
	//获取和更新生产计划
	public function GetProductPlan($id,Request $request)
    {
		if(empty($request->submit)){
			$manufacture = Manufacture::where('manufactures.id', $id)
							->leftJoin('customers', 'customers.id', '=', 'manufactures.customer_id')
							->select('manufactures.no','manufactures.created_at')
							->first();
			if(empty($manufacture))return $this->response->array(array('code'=>100,'error'=>'error','msg'=>'无效生产任务书'));
			$proplan = Product_plan::where('product_plans.manufacture_id', $id)
							->join('parts', 'parts.id', '=', 'product_plans.part_id')
							->join('orders', 'orders.id', '=', 'parts.order_id')
							->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
							->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
							->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
							->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
							->select('product_plans.id','parts.name','parts.volume_size','parts.requirements','parts.product_num','parts.diagram','parts.start_date','parts.due_date','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'orders.no as orderno','product_plans.jh_data as jhdata','product_plans.start_data as plstatime','product_plans.end_data as plendtime','product_plans.status as plstat','product_plans.real_edate as realedate')
							->get();
			
			$out = $manufacture->toArray();
			$out['proplan'] = $proplan->toArray();

			return $this->response->array($out);
		}else{
			foreach($request->proplan as $val){
				$tpart = Product_plan::find($val['id']);
				if($tpart['status'] >= 1)continue;
				if(empty($val['plstatime']) || empty($val['plendtime']))continue;
				$upd = array();
				$upd['start_data']=$val['plstatime'];
				$upd['end_data']=$val['plendtime'];
				$upd['real_sdate']=$val['plstatime'];
				$upd['status']=1;
				Product_plan::where('id', $val['id'])->update($upd);
				$paid = $tpart->part_id;
				$order = Order::where('parts', 'like', '%'.$paid.'%')->first();
				if(!empty($order->status) && $order->status != 3){
					$order->status = 3;
					$order->save();
					$mfa = Manufacture::where('manufactures.id', $id)->value('customer_id');
					$url = 'https://yp-dev.one2fit.cn/customer/orderlist';
					$this->SendWechatMessage(array('utype'=>'customer','uid'=>$mfa),1,3,$order->no,$url);
				}
			}
			$mafa = Manufacture::find($id);
			if($mafa->status < 1)
				Manufacture::where('manufactures.id', $id)->update(['status'=>1]);
			return $this->response->array(array('code'=>200,'success'=>'ok'));
		}

	}
	//生产计划设置完成
	public function SetProductPlanOk(Request $request)
    {
		$this->validate($request, [
			'plenid' => 'required',
		]);
		if(is_array($request->plenid)){
			$out = array();
			foreach($request->plenid as $val){
				$maid = '';
				$prtid = '';
				$endtim = date("Y-m-d H:i:s");
				$plan = Product_plan::find($val);
				$maid = $plan->manufacture_id;
				$prtid = $plan->part_id;
				$plan->real_edate = $endtim;
				$plan->status = 2;
				$plan->save();
				Part::where('id', $prtid)->update(['status'=>2]);
				$manusta = Product_plan::where('manufacture_id',$maid)->select('real_edate')->get();
				$plendtime = $manusta->pluck('real_edate');
				$plendtime = $plendtime->diff([null]);
				if($manusta->count() == $plendtime->count()){
					Manufacture::where('manufactures.id', $maid)->update(['status'=>2]);
					$order = Order::where('parts', 'like', '%'.$prtid.'%')->first();
					$parts = Part::whereIn('parts.id',  json_decode($order['parts'],true))->select('status')->get();
					if($parts->avg('status') == 2 && $order->status < 4){
						$order->status = 4;
						$order->save();
						$url = url('/wechat/order/procuplan').'?val='.encrypt($order->id);
						$this->SendWechatMessage(array('utype'=>'customer','uid'=>$order->customer_id),1,4,$order->no,$url);
					}
				}
				array_push($out,array('id'=>$val,'realedate'=>$endtim));
			}
			return $this->response->array($out);
		}else{
			$maid = '';
			$prtid = '';
			$endtim = date("Y-m-d H:i:s");
			$plan = Product_plan::find($request->plenid);
			$maid = $plan->manufacture_id;
			$prtid = $plan->part_id;
			$plan->real_edate = $endtim;
			$plan->status = 2;
			$plan->save();
			Part::where('id', $prtid)->update(['status'=>2]);
			$manusta = Product_plan::where('manufacture_id',$maid)->select('real_edate')->get();
			$plendtime = $manusta->pluck('real_edate');
			$plendtime = $plendtime->diff([null]);
			if($manusta->count() == $plendtime->count()){
				Manufacture::where('manufactures.id', $maid)->update(['status'=>2]);
				$order = Order::where('parts', 'like', '%'.$prtid.'%')->first();
				$parts = Part::whereIn('parts.id',  json_decode($order['parts'],true))->select('status')->get();
				if($parts->avg('status') == 2 && $order->status < 4){
					$order->status = 4;
					$order->save();
					$url = url('/wechat/order/procuplan').'?val='.encrypt($order->id);
					$this->SendWechatMessage(array('utype'=>'customer','uid'=>$order->customer_id),1,4,$order->no,$url);
				}
			}
		}
		return $this->response->array(['realedate'=>$endtim]);
	}
	public function UploadPic(Request $request)
	{
		$this->validate($request, [
			'file' => 'required',
			'plenid' => 'required',
		]);
		$file = $request->file('file');
		$allowed_extensions = ["png", "jpg", "jpeg","gif","mp4","mov"];
		if ($file->getClientOriginalExtension() && !in_array(strtolower($file->getClientOriginalExtension()), $allowed_extensions)) {
			return $this->response->errorUnauthorized('上传错误：只允许上传图片或视频。');
		}
		$path = $file->store('public/complete/'.date('Ymd'));
		$url = env('APP_URL').Storage::url($path);
		$planid = $request->plenid;
		DB::transaction(function() use (&$planid,&$url){
			$plan = Product_plan::where('id',$planid)->select('id','pics')->lockForUpdate()->first();

			if(empty($plan))return $this->response->errorUnauthorized('生产计划错误：找不到该生产计划');
			$pics = json_decode($plan->pics,true);
			if(empty($pics)){
				$pics = array($url);
			}else{
				array_push($pics,$url);
			}
			if(count($pics)>9)return $this->response->errorUnauthorized('只能上传9个图片或视频！');
			$plan->pics = json_encode($pics);
			$plan->save();
		}, 5);
		
		return $this->response->array(['message'=>$url]);
	}
	public function DeletePic(Request $request)
	{
		$this->validate($request, [
			'filepath' => 'required',
			'plenid' => 'required',
		]);
		$plan = Product_plan::where('id',$request->plenid)->select('id','pics')->first();
		if(empty($plan))return $this->response->errorUnauthorized('生产计划错误：找不到该生产计划');
		$repath  = $request->filepath;
		$fname =strrchr($repath,"complete");
		Storage::delete('public/'.$fname);
		$pics = json_decode($plan->pics,true);
		
		$plan->pics = json_encode(array_values(array_diff($pics,array($request->filepath))));
		$plan->save();
		return $this->response->array(['message'=>'删除成功']);
	}
	public function GetPic(Request $request)
	{
		$this->validate($request, [
			'plenid' => 'required',
		]);
		$plan = Product_plan::where('id',$request->plenid)->select('id','pics')->first();
		if(empty($plan))return $this->response->errorUnauthorized('生产计划错误：找不到该生产计划');
		$pics = json_decode($plan->pics,true);
		$imgs = array();
		$videos = array();
		if(!empty($plan->pics)){
			foreach($pics as $val){
				$extensions = substr($val,-3);
				switch($extensions){
					case "png":
					case "jpg":
					case "peg":
					case "gif":
						array_push($imgs,$val);
						break;
					case "mp4":
					case "mov":
						array_push($videos,$val);
						break;
				}
			}
		}
		return $this->response->array(['images'=>$imgs,'videos'=>$videos]);
	}
}
