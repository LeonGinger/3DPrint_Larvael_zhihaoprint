<?php

namespace App\Http\Controllers\Api\V1;

use Storage;
use Illuminate\Http\Request;
use App\Http\Transformers\AdminUserTransformer;
use App\Models\Customer;
use App\Models\Linkman;
use App\Models\User;
use App\Models\Materials;
use App\Models\Equipment;
use App\Models\Order;
use App\Models\Part;
use App\Models\Quotation;
use App\Models\Tenant;
use App\Models\TenantLevel;
use App\Models\PlantFormSetting;
use App\Models\TenantTemp;
use App\Models\Wechatbind;
use App\Models\Evaluate;
use App\Models\ReturnNote;
use App\Models\Fastquotation;
use App\Models\Fastpart;
use Overtrue\Pinyin\Pinyin;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Notifications\InvoicePaid;
use Illuminate\Notifications\Notification;
use EasyWeChat\Factory;

class SalesOrderController extends Controller
{
    public function SalesOrderList(Request $request)
    {
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('orde') && $search['orde'] = $request->get('orde');
			$request->get('edat') && $search['edat'] = $request->get('edat');
			$request->get('cont') && $search['cont'] = $request->get('cont');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
			
        $orders = Order::where(function($query) use (&$search){
						$query->where('orders.tenat_id', $this->GetLoginUser()->tenant_id);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'orde':
									$query->where('orders.no', '=', $val);break;
								case 'cont':
									$query->where('orders.contract', '<>', null);break;
								case 'stat':
									$query->where('orders.start', '=', $val);break;
								case 'edat':
									$query->whereDate('orders.created_at', '=' , $val);break;
							}
						}
					})
					->join('customers', 'customers.id', '=', 'orders.customer_id')
					->select('orders.*','customers.name as cusname')
					->orderBy('orders.id', 'desc')
					->paginate(5);
		$orders->appends($search)->links();
		if(empty($orders))return $this->response->errorUnauthorized('无此订单');
		
		
		return $this->response->array($orders);
    }
	//销售部-添加订单-获取客户列表
	public function ListCustom()
    {
		$list=Customer::where('tenat_id', $this->GetLoginUser()->tenant_id)
						->where('status',1)
						->select('id','name')->get();
        return $this->response->array($list->toArray());
    }
	//销售部-添加订单-获取客户联系人
	public function ListLinkman($id)
    {
		$lkm=Linkman::where('customer_id', $id)->select('id','linkman_name','lk_type','lk_phone','lk_address','defvue')->get();
		$lxr = $shr = $list = array();
		foreach($lkm as $i){
			if($i['lk_type'] == 0)
				$lxr[] = $i;
			else
				$shr[] = $i;
		}
		$list['linkman'] = $lxr;
		$list['consignee'] = $shr;
		$list['coefficient'] = Customer::where('id',$id)->value('coefficient');
		$list['saveinfo'] = json_decode(Customer::where('id',$id)->value('saveinfo'),true);
        return $this->response->array($list);
	}
	//销售部-添加订单-获取部门用户列表
	public function ListDepusers()
    {
		$users = User::where('tenant_id', $this->GetLoginUser()->tenant_id)->where('status',1)->select('id','name')->get();
		return $this->response->array($users->toArray());
	}
	//销售部-添加订单-获取成型材料列表
	public function ListMaterials($id)
    {
		$list=Materials::where('mold_id',$id)->where('status',1)->select('id','name','price','density')->get();
        return $this->response->array($list->toArray());
    }
	//销售部-添加订单-获取成型设备列表
	public function ListEquipment($id)
    {
		$list=Equipment::where('mold_id',$id)->where('status',1)->select('id','mname')->get();
        return $this->response->array($list->toArray());
    }
	//销售部-添加订单
	public function OrderAdd(Request $request)
    {

			$this->validate($request, [
				'customer_id' => 'required',
				'lkmans' => 'required',
				'postaddr' => 'required|numeric',
				'invoaddr' => 'required|numeric',
				'manger' => 'required|numeric',
				'pojer' => 'required|numeric',
				'saler' => 'required|numeric',
				'taxation' => 'required',
				'freight' => 'required',
				'parts' => 'required',
			]);
			
			//总计
			$tolicon = 0;
			
			//添加订单
			$order = new Order;
			$order->tenat_id = $this->GetLoginUser()->tenant_id;
			$order->customer_id = $request->customer_id;
			$order->no = date("YmdHis").rand(1111,9999);
			$order->lkmans = $request->lkmans;
			$order->postaddr = $request->postaddr;
			$order->invoaddr = $request->invoaddr;
			$order->manger = $request->manger;
			$order->pojer = $request->pojer;
			$order->saler = $request->saler;
			$order->parts = 'orz';
			$order->taxation = $request->taxation;
			$order->freight = $request->freight;
			$order->save();
			
			$tolicon = $request->taxation + $request->freight;
			
			//添加零件
			$cid = $order->id;
			$parts = $request->parts;
			$erom = '';
			$coefficient = 1;
			foreach($parts as $item){
				if(empty($item['material_id'])){$erom = "成型材质不能为空！";break;};
				if(empty($item['surface_id'])){$erom = "表面处理不能为空！";break;};
				if(empty($item['molding_process_id'])){$erom = "成型工艺不能为空！";break;};
				if(empty($item['equipment_id'])){$erom = "成型设备不能为空！";break;};
				if(empty($item['name'])){$erom = "名称不能为空！";break;};
				if(empty($item['diagram'])){$erom = "简图不能为空！";break;};
				if(empty($item['volume_size'])){$erom = "体积尺寸不能为空！";break;};
				if(empty($item['coefficient'])){$erom = "系数不能为空！";break;};
				if(empty($item['price'])){$erom = "价格不能为空！";break;};
				if(empty($item['product_num'])){$erom = "生成数量不能为空！";break;};
				if(empty($item['start_date'])){$erom = "启动日期不能为空！";break;};
				if(empty($item['due_date'])){$erom = "交货日期不能为空！";break;};
			}
			
			$saveinfo = array();
			if(empty($erom)){
				foreach($parts as $item){
					$tp = new Part([
						'order_id' => $cid ,
						'material_id'=>$item['material_id'],//成型材质id
						'surface_id'=>$item['surface_id'],//表面处理id
						'molding_process_id'=>$item['molding_process_id'],//成型工艺id
						'equipment_id'=>$item['equipment_id'],//成型设备id
						'name'=>$item['name'],//零件名称
						'diagram'=>$item['diagram'],//简图
						'volume_size'=>json_encode($item['volume_size']),//体积尺寸
						'coefficient'=>$item['coefficient'],//价格系数
						'price'=>$item['price'],//价格
						'product_num'=>$item['product_num'],//生产数量
						'start_date'=>$item['start_date'],//启动日期
						'due_date'=>$item['due_date'],//交货日期
						'remark'=>empty($item['remark']) ? null : $item['remark'],//备注
						'status'=>empty($item['status']) ? 0 : $item['status'],//零件状态
					]);
					if(empty($saveinfo)){
						$saveinfo['material_id'] = $item['material_id'];
						$saveinfo['surface_id'] = $item['surface_id'];
						$saveinfo['molding_process_id'] = $item['molding_process_id'];
						$saveinfo['equipment_id'] = $item['equipment_id'];
					}
					$coefficient = $item['coefficient'];
					$tp->save();
					$tolicon = $tolicon + $item['price'] * $item['product_num'];
				}
			}else{
				$ord = Order::find($cid);
				$ord->forceDelete();
				return $this->response->errorUnauthorized($erom);
			}
			//更新订单的零件字段
			$list=Part::where('order_id',$cid)->pluck('id');
			Order::where('id', $cid)->update(['parts' => $list]);
			Customer::where('id', $request->customer_id)->update(['coefficient' => $coefficient,'saveinfo'=>json_encode($saveinfo, JSON_NUMERIC_CHECK)]);
			
			//添加报价单
			//if(!file_exists(public_path('qrcodes')))
			//	mkdir(public_path('qrcodes'));
			//$filename = date("YmdHis").rand(1111,9999).'.png';
			//$qurul = env('APP_URL').'/qrcodes/'.$filename;
			//QrCode::format('png')->size(488)->generate('http://yp-dev.one2fit.cn/quotation?val='.encrypt($cid),public_path('qrcodes/'.$filename));
			
			$wuser = new Wechatbind;
			$wuser->uid = $cid;
			$wuser->type = 'lookquota';
			$wuser->save();
			$app = Factory::officialAccount(config('wechat'));
			$result = $app->qrcode->temporary($wuser->id,  30 * 24 * 3600);
			$qurul = $app->qrcode->url($result['ticket']);
			
			$pinyin = new Pinyin();
			$jc = $pinyin->abbr($this->GetLoginUser()->name, PINYIN_KEEP_ENGLISH);
			$quotno = date("Ymd").'-'.Customer::where('id', $request->customer_id)->value('no').'-'.Tenant::where('id', $this->GetLoginUser()->tenant_id)->value('daihao').'-'.strtoupper($jc);
			
			$tp = TenantTemp::where('tenant_id',$this->GetLoginUser()->tenant_id)->first();
			$qs = '';
			$qt = '';
			if(empty($tp)){
				$pfs = PlantFormSetting::first();
				$qs = $pfs->baojia_template_text1;
				$qt = $pfs->baojia_template_text2;
			}else{
				if(empty($tp->text1)){
					$pfs = PlantFormSetting::first();
					$qs = $pfs->baojia_template_text1;
				}else{
					$qs = $tp->text1;
				}
				if(empty($tp->text2)){
					$pfs = PlantFormSetting::first();
					$qt = $pfs->baojia_template_text2;
				}else{
					$qt = $tp->text2;
				}
			}
			
			$quot = new Quotation;
			$quot->order_id = $cid;
			$quot->no = $quotno;
			$quot->qrcode_url = $qurul;
			$quot->qs = $qs;
			$quot->qt = $qt;
			$quot->total = $tolicon;
			$quot->save();
			
			return $this->response->array(array('code'=>200,'success'=>'ok','order_id'=>$cid));


    }
	public function GetQuotations($id)
    {
		$order = Order::find($id);
		if(empty($order))return $this->response->errorUnauthorized('无订单信息');
		$quot = Quotation::where('order_id', $id)->first();
		if(empty($quot))return $this->response->errorUnauthorized('报价单错误');
		$cus = Customer::where('id', $order->customer_id)->select('name','ticket_info')->first();
		$lks = explode(',', $order->lkmans);
		$lks = Linkman::whereIn('id', $lks)->pluck('linkman_name');
		
		$parts = Part::whereIn('parts.id', json_decode($order->parts,true))
						->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
						->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
						->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
						->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
						->leftJoin('changelogs', 'changelogs.part_id', '=', 'parts.id')
						->select('parts.*','materials.name as matname','materials.density as density','materials.density as mprice',  'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname', 'changelogs.product_num as cproduct', 'changelogs.remark as cremark')
						->get();
		
		$out = $quot->toArray();
		$out['cusname'] = $cus->name;
		$out['ticket_info'] = unserialize($cus->ticket_info);
		$out['linkmans'] = $lks;
		$out['postaddr'] = Linkman::where('id', $order->postaddr)->select('linkman_name','lk_phone','province','city','area','lk_address')->first();
		$out['invoaddr'] = Linkman::where('id', $order->invoaddr)->select('linkman_name','lk_phone','province','city','area','lk_address')->first();
		$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
		$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
		$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
		$out['parts'] = $parts;
		$out['taxation'] = $order->taxation;
		$out['freight'] = $order->freight;

		return $this->response->array($out);
	}
	public function UpdateQuotations($id,Request $request)
    {
		$quot = Quotation::where('order_id',$id)->first();
		if($quot->status)return $this->response->errorUnauthorized('报价单已经审核通过');
		if(empty($quot))return $this->response->errorUnauthorized('报价单错误');
		$quot->qs=empty($request->qs) ? null : $request->qs;
		$quot->qt=empty($request->qt) ? null : $request->qt;
		$quot->status=empty($request->status) ? 0 : $request->status;
		if($request->status == 1){
			$tid = $this->GetLoginUser()->tenant_id;
			$onum = Order::where('tenat_id',$tid)->count();
			$tlv = Tenant::where('id',$tid)->whereDate('expired_at','>',now())->value('tenant_level_id');
			if(empty($tlv)) $tlv = 1;
			$lvid = TenantLevel::find($tlv);
			if($onum > $lvid->order_numbers)$this->response->errorUnauthorized('您的总订单数量已经达到上限，请升级后继续使用。');
			
			$cusid = Order::where('id',$quot->order_id)->select('id','customer_id','no')->first();
			$url = url('/quotation').'?val='.encrypt($cusid->id);
			$this->SendWechatMessage(array('utype'=>'customer','uid'=>$cusid->customer_id),2,1,$cusid->no,$url);
		}
		$quot->save();
		$odupd = array();
		if($request->status !== null){
			switch($request->status){
				case 0:
					$odupd['status'] = 0;
					break;
				case 1:
					$odupd['status'] = 1;
					break;
			}
		}
		Order::where('id',$id)->update($odupd);
		return $this->response->array(['message'=>'ok']);
	}
	public function UpdateQuotationsQrurl(Request $request)
    {
		$this->validate($request, [
				'oid' => 'required',
			]);
		
		$ord = Order::find($request->oid);
		if(empty($ord))return $this->response->errorUnauthorized('订单错误');
		$quot = Quotation::where('order_id',$request->oid)->first();
		if(empty($quot))return $this->response->errorUnauthorized('报价单错误');
		
		$wuser = new Wechatbind;
		$wuser->uid = $request->oid;
		$wuser->type = 'lookquota';
		$wuser->save();
		$app = Factory::officialAccount(config('wechat'));
		$result = $app->qrcode->temporary($wuser->id,  30 * 24 * 3600);
		$qurul = $app->qrcode->url($result['ticket']);
		
		$quot->qrcode_url = $qurul;
		$quot->save();
		return $this->response->array(['message'=>$qurul]);
	}
	public function GetOrders(Request $request)
    {
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('orde') && $search['orde'] = $request->get('orde');
			$request->get('edat') && $search['edat'] = $request->get('edat');
			$request->get('cont') && $search['cont'] = $request->get('cont');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		$orders = Order::where(function($query) use (&$search){
						$query->where('orders.tenat_id', $this->GetLoginUser()->tenant_id);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'orde':
									$query->where('orders.no', '=', $val);break;
								case 'cont':
									$query->where('orders.contract', '<>', null);break;
								case 'stat':
									$query->where('orders.start', '=', $val);break;
								case 'edat':
									$query->whereDate('orders.created_at', '=' , $val);break;
							}
						}
					})
					->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
					->select('orders.id','customers.name as cusname','orders.no','orders.status','orders.contract','orders.created_at')
					->orderBy('id', 'desc')
					->paginate(10);
		$orders->appends($search)->links();
		if(empty($orders))return $this->response->errorUnauthorized('无此订单');
		
		
		return $this->response->array($orders);
	}
	public function EditOrder($id,Request $request)
    {
		$order = Order::find($id);
		if(empty($order))return $this->response->errorUnauthorized('无此订单');
		if($order->status != 0)return $this->response->errorUnauthorized('此订单不能修改');
		$quot = Quotation::where('order_id', $id)->first();
		if(empty($quot))return $this->response->errorUnauthorized('报价单错误');
		
		$parts = Part::whereIn('parts.id', json_decode($order->parts,true))
						->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
						->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
						->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
						->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
						->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname')
						->get();
		
		$out = $order->toArray();
		$out['parts'] = $parts->toArray();
		
		if(empty($request->submit)){
			return $this->response->array($out);
		}
		else{
			$this->validate($request, [
				'customer_id' => 'required',
				'lkmans' => 'required',
				'postaddr' => 'required|numeric',
				'invoaddr' => 'required|numeric',
				'manger' => 'required|numeric',
				'pojer' => 'required|numeric',
				'saler' => 'required|numeric',
				'taxation' => 'required',
				'freight' => 'required',
				'parts' => 'required',
			]);
			
			//总计
			$tolicon = 0;
			
			$order->customer_id = $request->customer_id;
			$order->lkmans = $request->lkmans;
			$order->postaddr = $request->postaddr;
			$order->invoaddr = $request->invoaddr;
			$order->manger = $request->manger;
			$order->pojer = $request->pojer;
			$order->saler = $request->saler;
			$order->taxation = $request->taxation;
			$order->freight = $request->freight;
			$order->save();
			
			$tolicon = $request->taxation + $request->freight;
			
			$parts = $request->parts;
			foreach($parts as $item){
				$part = new Part();
				$upd = $part::find($item['id']);
				$remak = empty($item['remark'])? null:$item['remark'];
				if(!empty($upd)){
					$upd->update([
									'material_id'=>$item['material_id'],//成型材质id
									'surface_id'=>$item['surface_id'],//表面处理id
									'molding_process_id'=>$item['molding_process_id'],//成型工艺id
									'equipment_id'=>$item['equipment_id'],//成型设备id
									'name'=>$item['name'],//零件名称
									'diagram'=>$item['diagram'],//简图
									'volume_size'=>json_encode($item['volume_size']),//体积尺寸
									'coefficient'=>$item['coefficient'],//价格系数
									'price'=>$item['price'],//价格
									'product_num'=>$item['product_num'],//生产数量
									'start_date'=>$item['start_date'],//启动日期
									'due_date'=>$item['due_date'],//交货日期
									'remark'=>$remak,//备注
								]);
					$tolicon = $tolicon + $item['coefficient'] * $item['price'] * $item['product_num'];
				}else{
					$part = new Linkman([
						'order_id' => $id ,
						'material_id'=>$item['material_id'],//成型材质id
						'surface_id'=>$item['surface_id'],//表面处理id
						'molding_process_id'=>$item['molding_process_id'],//成型工艺id
						'equipment_id'=>$item['equipment_id'],//成型设备id
						'name'=>$item['name'],//零件名称
						'diagram'=>$item['diagram'],//简图
						'volume_size'=>json_encode($item['volume_size']),//体积尺寸
						'coefficient'=>$item['coefficient'],//价格系数
						'price'=>$item['price'],//价格
						'product_num'=>$item['product_num'],//生产数量
						'start_date'=>$item['start_date'],//启动日期
						'due_date'=>$item['due_date'],//交货日期
						'remark'=>$remak,//备注
						'status'=>0,//零件状态
					]);
					$part->save();
					$tolicon = $tolicon + $item['coefficient'] * $item['price'] * $item['product_num'];
				}
			}
			//更新报价单合计金额
			$quot = Quotation::where('order_id',$id)->first();
			$quot->total = $tolicon;
			$quot->save();
			//更新订单的零件字段
			$list=Part::where('order_id',$id)->pluck('id');
			Order::where('id', $id)->update(['parts' => $list]);
	
			return $this->response->array(['message'=>'ok']);
		}
		
	}
	public function DelOrder($id)
    {
		$post = Order::destroy($id);
		Quotation::where('order_id',$id)->delete();
		if($post){
			return $this->response->array(['message'=>'ok']);
		}else{
			return $this->response->array(['message'=>'ok']);
		}
    }
	public function ChackOrder(Request $request)
	{
		$this->validate($request, [
				'qid' => 'required',
			]);
		$tid = $this->GetLoginUser()->tenant_id;
		$onum = Order::where('tenat_id',$tid)->count();
		$tlv = Tenant::where('id',$tid)->whereDate('expired_at','>',now())->value('tenant_level_id');
		if(empty($tlv)) $tlv = 1;
		$lvid = TenantLevel::find($tlv);
		if($onum > $lvid->order_numbers)$this->response->errorUnauthorized('您的总订单数量已经达到上限，请升级后继续使用。');
		
		$oids = array_wrap($request->qid);
		Quotation::whereIn('order_id',$oids)->where('status',0)->update(['status' => 1]);
		Order::whereIn('id',$oids)->where('status',0)->update(['status' => 1]);
		foreach($oids as $oid){
			$cusid = Order::where('id',$oid)->select('id','customer_id','no')->first();
			$url = url('/quotation').'?val='.encrypt($cusid->id);
			$this->SendWechatMessage(array('utype'=>'customer','uid'=>$cusid->customer_id),2,1,$cusid->no,$url);
		}
		return $this->response->array(['message'=>'ok']);
	}
	//查看订单评价
	public function LookOrderEvaluate(Request $request)
	{
		$this->validate($request, [
				'oid' => 'required',
			]);
		$eva = Evaluate::where('order_id',$request->oid)->select('content','pingjia','manger_star','pojer_star','saler_star')->first();
		if(empty($eva))
			return $this->response->errorUnauthorized('客户未评价');
		else
			return $this->response->array($eva->toArray());
	}
	//评价列表
	public function OrderEvaluateList(Request $request)
	{
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('orde') && $search['orde'] = $request->get('orde');
			$request->get('edat') && $search['edat'] = $request->get('edat');
			$request->get('pija') && $search['pija'] = $request->get('pija');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		$eva = Evaluate::where(function($query) use (&$search){
						$query->where('evaluates.tenat_id', $this->GetLoginUser()->tenant_id);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'orde':
									$query->where('orders.no', '=', $val);break;
								case 'pija':
									$query->where('evaluates.pingjia', '<>', null);break;
								case 'stat':
									$query->where('orders.start', '=', $val);break;
								case 'edat':
									$query->whereDate('evaluates.created_at', '=' , $val);break;
							}
						}
					})
					->leftJoin('customers', 'customers.id', '=', 'evaluates.customer_id')
					->leftJoin('orders', 'orders.id', '=', 'evaluates.order_id')
					->select('evaluates.id','orders.id as orderid','customers.name as cusname','orders.no as orderno','evaluates.pingjia','evaluates.content','evaluates.created_at')
					->orderBy('id', 'desc')
					->paginate(10);
		$eva->appends($search)->links();
		if(empty($eva))return $this->response->errorUnauthorized('错误操作');
		
		return $this->response->array($eva);
	}
	//获取售后服务列表
	public function OrderAfterSale(Request $request)
	{
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('orde') && $search['orde'] = $request->get('orde');
			$request->get('edat') && $search['edat'] = $request->get('edat');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		$ren = ReturnNote::where(function($query) use (&$search){
						$query->where('return_notes.tenat_id', $this->GetLoginUser()->tenant_id);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'orde':
									$query->where('orders.no', '=', $val);break;
								case 'stat':
									$query->where('return_notes.status', '=', $val);break;
								case 'edat':
									$query->whereDate('return_notes.created_at', '=' , $val);break;
							}
						}
					})
					->leftJoin('customers', 'customers.id', '=', 'return_notes.commit_user_id')
					->leftJoin('orders', 'orders.id', '=', 'return_notes.order_id')
					->select('return_notes.id','orders.id as orderid','customers.name as cusname','orders.no as orderno','return_notes.parts','return_notes.returned_type','return_notes.remark','return_notes.nochack','return_notes.status','return_notes.created_at')
					->orderBy('return_notes.id', 'desc')
					->paginate(10);
		$ren->appends($search)->links();
		if(empty($ren))return $this->response->errorUnauthorized('错误操作');
		$tmp = $ren->toArray();
		foreach($tmp['data'] as $key => $val){
			if(!empty($val['parts'])){
				$tpid = json_decode($val['parts'],true);
				$reparts = Part::whereIn('parts.id', array_keys($tpid))->select('parts.id','parts.name','parts.product_num','parts.diagram')->get();
				foreach($reparts as $k=>$v){
					$reparts[$k]['product_num'] = $tpid[$v['id']];
				}
				$tmp['data'][$key]['parts'] = $reparts->toArray();
			}
		}
		return $this->response->array($tmp);
	}
	//获取新售后服务数量
	public function OrderAfterSaleNewNum()
	{
		
		$ren = ReturnNote::where('tenat_id', $this->GetLoginUser()->tenant_id)
					->where('status', 0)
					->count();
		return $this->response->array($ren);
	}
	//审核退单或重做
	public function OrderAfterSaleCheck(Request $request)
	{
		$this->validate($request, ['rid' => 'required','status' => 'required']);
		$ren = ReturnNote::find($request->rid);
		$ord = Order::find($ren->order_id);

		if($ren->status > 0) return $this->response->errorUnauthorized('已经审核过了');
		if($request->status == 1){
			if($ren->returned_type == 0){
				$ord->status = 8;
				$ord->save();
				$ren->status = 1;
				$ren->save();
				$url = 'https://yp-dev.one2fit.cn/admin/#/modules/index';
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->saler),1,20,$ord->no,$url);
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->pojer),1,20,$ord->no,$url);
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->manger),1,20,$ord->no,$url);
				$this->SendWechatMessage(array('utype'=>'customer','uid'=>$ord->customer_id),1,21,$ord->no,url('/wechat/order/aftersale').'?val='.encrypt($ord->id));
			}elseif($ren->returned_type == 1){
				$parts = json_decode($ord->parts,true);
				$tpid = json_decode($ren->parts,true);
				foreach($tpid as $key=>$val){
					if(!empty($val) && (int)$val){
						$tp = Part::find($key);
						$pa = new Part;
						$pa->order_id = $tp->order_id;
						$pa->material_id = $tp->material_id;
						$pa->surface_id = $tp->surface_id;
						$pa->molding_process_id = $tp->molding_process_id;
						$pa->equipment_id = $tp->equipment_id;
						$pa->name = $tp->name.'-重做';
						$pa->diagram = $tp->diagram;
						$pa->volume_size = $tp->volume_size;
						$pa->status = 0;
						$pa->coefficient = 0;
						$pa->price = 0;
						$pa->product_num = $val;
						$pa->start_date = $tp->start_date;
						$pa->due_date = $tp->due_date;
						$pa->save();
						array_push($parts,$pa->id);
					}
				}
				$ord->update(['parts' => json_encode($parts, JSON_NUMERIC_CHECK)]);
				$ren->newparts = json_encode($parts, JSON_NUMERIC_CHECK);
				$ren->status = 1;
				$ren->save();
				$url = 'https://yp-dev.one2fit.cn/admin/#/modules/index';
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->saler),1,22,$ord->no,$url);
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->pojer),1,22,$ord->no,$url);
				$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->manger),1,22,$ord->no,$url);
				$this->SendWechatMessage(array('utype'=>'customer','uid'=>$ord->customer_id),1,23,$ord->no,url('/wechat/order/aftersale').'?val='.encrypt($ord->id));
			}
		}elseif($request->status == 2){
			$ren->status = 2;
			$ren->nochack = $request->message;
			$ren->save();
			$this->SendWechatMessage(array('utype'=>'customer','uid'=>$ord->customer_id),1,31,$ord->no,url('/wechat/order/aftersale').'?val='.encrypt($ord->id));
		}
		return $this->response->array(['message'=>'ok']);
	}
	
	//销售部-快速添加订单
	public function FastOrderAdd(Request $request)
    {
		$this->validate($request, ['parts' => 'required']);
		
		//总计
		$tolicon = 0;
		$taxation = $request->taxation ? $request->taxation : 0;
		$freight = $request->freight ? $request->freight : 0;
		$qt = '';
		$qs = '';
		
		//$tenid= $this->GetLoginUser()->tenant_id;
		$tenid=0;
		$tp = TenantTemp::where('tenant_id',$tenid)->first();
		if(empty($tp)){
			$pfs = PlantFormSetting::first();
			$qs = $pfs->baojia_template_text1;
			$qt = $pfs->baojia_template_text2;
		}else{
			if(empty($tp->text1)){
				$pfs = PlantFormSetting::first();
				$qs = $pfs->baojia_template_text1;
			}else{
				$qs = $tp->text1;
			}
			if(empty($tp->text2)){
				$pfs = PlantFormSetting::first();
				$qt = $pfs->baojia_template_text2;
			}else{
				$qt = $tp->text2;
			}
		}
		
		//添加订单
		$faorder = str_random(8);
		$order = new Fastquotation;
		$order->tenat_id = $tenid;
		$order->fast_id = $faorder;
		$order->parts = 'orz';
		$order->qt = $qt;
		$order->qs = $qs;
		$order->total = 0;
		$order->save();
		
		$tolicon = $taxation + $freight;
		
		//添加零件
		$cid = $order->id;
		$parts = $request->parts;
		$erom = '';
		foreach($parts as $item){
			if(empty($item['material_id'])){$erom = "成型材质不能为空！";break;};
			if(empty($item['name'])){$erom = "名称不能为空！";break;};
			if(empty($item['diagram'])){$erom = "简图不能为空！";break;};
			if(empty($item['volume_size'])){$erom = "体积尺寸不能为空！";break;};
			if(empty($item['price'])){$erom = "价格不能为空！";break;};
			if(empty($item['product_num'])){$erom = "生成数量不能为空！";break;};
		}
		if(empty($erom)){
			foreach($parts as $item){
				$tp = new Fastpart([
					'fq_id' => $cid ,
					'material_id'=>$item['material_id'],//成型材质id
					'name'=>$item['name'],//零件名称
					'diagram'=>$item['diagram'],//简图
					'volume_size'=>json_encode($item['volume_size']),//体积尺寸
					'coefficient'=>empty($item['coefficient']) ? 1 : $item['coefficient'],//价格系数
					'price'=>$item['price'],//价格
					'product_num'=>$item['product_num'],//生产数量
					'remark'=>empty($item['remark']) ? null : $item['remark'],//备注
					'status'=>empty($item['status']) ? 0 : $item['status'],//零件状态
				]);
				$tp->save();
				$tolicon = $tolicon + $item['price'] * $item['product_num'];
			}
		}else{
			$ord = Fastquotation::find($cid);
			$ord->forceDelete();
			return $this->response->errorUnauthorized($erom);
		}
		//更新订单的零件字段
		$list=Fastpart::where('fq_id',$cid)->pluck('id');
		Fastquotation::where('id', $cid)->update(['parts' => $list,'total'=>$tolicon]);

		return $this->response->array(array('code'=>200,'success'=>'ok','faorder'=>$faorder,'path'=>url('/wechat/fastorder').'?val='.encrypt('1998|'.$cid)));
    }
	
	//销售部-再次下单功能-获取订单零件列表
	public function RloadOrderPartsList(Request $request)
    {
		$this->validate($request, ['oid' => 'required']);
		$parts = Part::where('parts.order_id', $request->oid)
						->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
						->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
						->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
						->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
						->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname')
						->get();
		return $this->response->array($parts->toArray());
	}
	public function RloadOrderAdd(Request $request)
    {
		$this->validate($request, [
			'oid' => 'required',
			'parts' => 'required',
		]);
		$oid = $request->oid;
		$ord = Order::find($oid);
		if(empty($ord))return $this->response->errorUnauthorized('无订单信息');
		$tolicon = 0;
		$nod = new Order;
		$nod->tenat_id = $ord->tenat_id;
		$nod->customer_id = $ord->customer_id;
		$nod->no = date("YmdHis").rand(1111,9999);
		$nod->lkmans = $ord->lkmans;
		$nod->postaddr = $ord->postaddr;
		$nod->invoaddr = $ord->invoaddr;
		$nod->manger = $ord->manger;
		$nod->pojer = $ord->pojer;
		$nod->saler = $ord->saler;
		$nod->parts = "orz";
		$nod->taxation = $ord->taxation;
		$nod->freight = $ord->freight;
		$nod->contract = null;
		$nod->status = 0;
		$nod->save();
		$tolicon = $ord->taxation + $ord->freight;
		$oldquot = Quotation::where('order_id',$oid)->first();
		$cid = $nod->id;
		$parts = array();
		foreach($request->parts as $val){
			if(!empty($val['product_num']) && (int)$val['product_num']){
				$tp = Part::find($val['id']);
				$pa = new Part;
				$pa->order_id = $cid;
				$pa->material_id = $tp->material_id;
				$pa->surface_id = $tp->surface_id;
				$pa->molding_process_id = $tp->molding_process_id;
				$pa->equipment_id = $tp->equipment_id;
				$pa->name = $tp->name;
				$pa->diagram = $tp->diagram;
				$pa->volume_size = $tp->volume_size;
				$pa->status = 0;
				$pa->coefficient = $tp->coefficient;
				$pa->price = $tp->price;
				$pa->product_num = $val['product_num'];
				$pa->start_date = $val['start_date'];
				$pa->due_date = $val['due_date'];
				$pa->save();
				array_push($parts,$pa->id);
				$tolicon = $tolicon + $tp->price * $val['product_num'];
			}
		}
		Order::where('id', $cid)->update(['parts' => json_encode($parts, JSON_NUMERIC_CHECK)]);
		
		//添加报价单
		if(!file_exists(public_path('qrcodes')))
			mkdir(public_path('qrcodes'));
		$filename = date("YmdHis").rand(1111,9999).'.png';
		$qurul = env('APP_URL').'/qrcodes/'.$filename;
		QrCode::format('png')->size(488)->generate('https://yp-dev.one2fit.cn/quotation?val='.encrypt($cid),public_path('qrcodes/'.$filename));
		
		$pinyin = new Pinyin();
		$jc = $pinyin->abbr($this->GetLoginUser()->name, PINYIN_KEEP_ENGLISH);
		$quotno = date("Ymd").'-'.Customer::where('id', $ord->customer_id)->value('no').'-'.Tenant::where('id', $this->GetLoginUser()->tenant_id)->value('daihao').'-'.strtoupper($jc);

		$quot = new Quotation;
		$quot->order_id = $cid;
		$quot->no = $quotno;
		$quot->qrcode_url = $qurul;
		$quot->qs = $oldquot->qs;
		$quot->qt = $oldquot->qt;
		$quot->total = $tolicon;
		$quot->save();
		
		return $this->response->array(['message'=>'下单成功！','order_id'=>$nod->id]);
	}
}
