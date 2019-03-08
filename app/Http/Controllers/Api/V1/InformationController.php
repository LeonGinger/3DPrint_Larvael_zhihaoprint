<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Transformers\CustomersTransformer;
use App\Http\Transformers\MaterialsTransformer;
use App\Models\Customer;
use App\Models\Linkman;
use App\Models\Molding;
use App\Models\Materials;
use App\Models\Surfaces;
use App\Models\Equipment;
use Illuminate\Validation\Rule;

class InformationController extends Controller
{
    public function CustomerList(Request $request)
    {
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('tel') && $search['tel'] = $request->get('tel');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		
		$list = Customer::where(function($query) use (&$search){
						$query->where('tenat_id', $this->GetLoginUser()->tenant_id);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('customers.name', 'like', '%'.$val.'%');break;
								case 'tel':
									$query->where('customers.tel', 'like', '%'.$val.'%');break;
								case 'stat':
									$query->where('customers.status', '<=' , $val);break;
							}
						}
					})
				->select('*')
				->orderBy('id', 'desc')
				->paginate(5);
		$list->appends($search)->links();
		return $this->response->array($list->toArray());
    }
	//信息部添加客户
	public function CustomerAdd(Request $request)
    {
			//联系人
			$lkman = array();
			$lkt = $request->linkman;
			if(!empty($lkt)){
				foreach($lkt as $item){
					if(empty($item['linkman_name']))return $this->response->errorUnauthorized('联系人名称不能为空');
					if(empty($item['lk_phone']))return $this->response->errorUnauthorized('联系电话不能为空！');
					if(empty($item['lk_address']))return $this->response->errorUnauthorized('联系地址不能为空！');
					$item['lk_type']=0;
					$lkman[]=$item;
				}
			}else{return $this->response->errorUnauthorized('联系人不能为空！');}
			$lkt = $request->consignee;
			if(!empty($lkt)){
				foreach($lkt as $item){
					if(empty($item['linkman_name']))return $this->response->errorUnauthorized('收货人名称不能为空！');
					if(empty($item['lk_phone']))return $this->response->errorUnauthorized('收货人电话不能为空！');
					if(empty($item['lk_address']))return $this->response->errorUnauthorized('收货人地址不能为空！');
					$item['lk_type']=1;
					$lkman[]=$item;
				}
			}else{return $this->response->errorUnauthorized('收货人不能为空！');}
			
			//添加客户信息
			$customer = new Customer;
			$customer->name = $request->name;
			$customer->province = $request->province;
			$customer->city = $request->city;
			$customer->area = $request->area;
			$customer->address = $request->address;
			$customer->tenat_id = $this->GetLoginUser()->tenant_id;
			$customer->customer_type_id = $request->customer_type_id;
			$customer->no = $request->no;
			$customer->status = $request->status;
			$customer->ticket_info = serialize($request->ticket_info);
			$customer->tel = $request->tel;
			$customer->save();
			
			//添加联系人和收货人
			$cid = $customer->id;
			foreach($lkman as $item){
				$lkm = new Linkman([
					'customer_id' => $cid ,
					'linkman_name'=>$item['linkman_name'],
					'lk_phone'=>$item['lk_phone'],
					'lk_address'=>$item['lk_address'],
					'lk_type'=>$item['lk_type'],
					'province'=>$item['province'],
					'city'=>$item['city'],
					'area'=>$item['area'],
					'email'=>$item['email'],
					'zipcode'=>$item['zipcode']
				]);
				$lkm->save();
			}
			
			return $this->response->array(['message'=>'ok']);
    }
	//编辑客户信息
	public function CustomerEdit($id,Request $request)
    {
		$customer = new Customer;
		$custd = $customer::findOrFail($id);
		$lkm = new Linkman;
		$lkm = $lkm->where('customer_id',$id)->get();
		$lxr = $shr =array();
		foreach($lkm as $i){
			if($i['lk_type'] == 0)
				$lxr[] = $i;
			else
				$shr[] = $i;
		}
		$cust = $custd->toArray();
		$cust['ticket_info'] = unserialize($cust['ticket_info']);
		$cust['linkman'] = $lxr;
		$cust['consignee'] = $shr;
		unset($cust['created_at']);
		unset($cust['updated_at']);
		
		if(empty($request->submit)){
			return $this->response->array($cust);
		}
		else{
			$custd->name = $request->name;
			$custd->province = $request->province;
			$custd->city = $request->city;
			$custd->area = $request->area;
			$custd->address = $request->address;
			$custd->customer_type_id = $request->customer_type_id;
			$custd->no = $request->no;
			$custd->status = $request->status;
			$custd->ticket_info = serialize($request->ticket_info);
			$custd->tel = $request->tel;
			$custd->save();

			$lkman = array();
			$lkt = $request->linkman;
			if(!empty($lkt)){
				foreach($lkt as $item){
					$item['lk_type']=0;
					$lkman[]=$item;
				}
			}
			$lkt = $request->consignee;
			if(!empty($lkt)){
				foreach($lkt as $item){
					$item['lk_type']=1;
					$lkman[]=$item;
				}
			}
			foreach($lkman as $item){
				$lkm = new Linkman();
				$upd = $lkm::find($item['id']);
				if(!empty($upd)){
					$upd->update(['linkman_name'=>$item['linkman_name'],'lk_phone'=>$item['lk_phone'],'lk_address'=>$item['lk_address'],'province'=>$item['province'],'city'=>$item['city'],'area'=>$item['area'],'email'=>$item['email'],'zipcode'=>$item['zipcode']]);
				}else{
					$lkm = new Linkman([
						'customer_id' => $id ,
						'linkman_name'=>$item['linkman_name'],
						'lk_phone'=>$item['lk_phone'],
						'lk_address'=>$item['lk_address'],
						'lk_type'=>$item['lk_type'],
						'province'=>$item['province'],
						'city'=>$item['city'],
						'area'=>$item['area'],
						'email'=>$item['email'],
						'zipcode'=>$item['zipcode']
					]);
					$lkm->save();
				}
			}
			return $this->response->array(['message'=>'ok']);
		}
	}
	//删除客户联系人
	public function CustomerDellxr($id)
    {
		$post = Linkman::destroy($id);
		if($post){
			return $this->response->array(['message'=>'ok']);
		}else{
			return $this->response->array(array('code'=>100,'success'=>'no'));
		}
    }
	//删除客户
	public function CustomerDel($id)
    {
		$post = Customer::destroy($id);
		if($post){
			return $this->response->array(['message'=>'ok']);
		}else{
			return $this->response->array(array('code'=>100,'success'=>'no'));
		}
    }
	//设置默认联系人
	public function SetDefaultLK(Request $request)
    {
		$this->validate($request, ['uid' => 'required|numeric|max:255','cid'=> 'required|numeric|max:255','type'=> 'required|numeric|max:255']);
		$use = Linkman::where([['customer_id', '=', $request->cid],['id', '=', $request->uid],['lk_type', '=', $request->type]])->first();
		if($use){
			Linkman::where('customer_id', $request->cid)->where('lk_type',$request->type)->update(['defvue' => 0]);
			$use->defvue = 1;
			$use->save();
			return $this->response->array(['message'=>'ok']);
		}
		return $this->response->array(['message'=>'empty']);
    }
	//添加成型材质
	public function AddMaterials(Request $request)
    {
			$this->validate($request, [
				'name' => 'required|unique:materials|max:255',
				'price' => 'required|numeric',
				'density' => 'required|numeric',
				'shape' => 'required|required',
				'moldid' => 'required|numeric',
			]);

			$materials = new Materials;
			$materials->name = $request->name;
			$materials->price = $request->price;
			$materials->density = $request->density;
			$materials->shape = $request->shape;
			$materials->mold_id = $request->moldid;
			$materials->tenat_id = $this->GetLoginUser()->tenant_id;
			$materials->status =$request->status ? $request->status : 0;
			$materials->save();
	
			return $this->response->array(['message'=>'ok']);
    }
	//编辑成型材质
	public function EditMaterials($id,Request $request)
    {
		$materials = new Materials;
		$mts = $materials::findOrFail($id);
		$mt = $mts->toArray();
		unset($mt['created_at']);
		unset($mt['updated_at']);
		if(empty($request->submit)){
			return $this->response->array($mt);
		}
		else{
			$uniq='';
			if($mts->name != $request->name)$uniq="unique:materials|";
			$this->validate($request, [
				'name' => 'required|'.$uniq.'max:255',
				'price' => 'required|numeric',
				'density' => 'required|numeric',
				'shape' => 'required',
				'moldid' => 'required|numeric',
			]);

			
			$mts->name = $request->name;
			$mts->price = $request->price;
			$mts->density = $request->density;
			$mts->shape = $request->shape;
			$mts->mold_id = $request->moldid;
			$mts->status =$request->status ? $request->status : 0;
			$mts->save();
	
			return $this->response->array(['message'=>'ok']);
		}
    }
	public function ListMaterials(Request $request)
    {
        $search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		
		$list = Materials::where(function ($query) {
							$query->where('tenat_id',0)
							->orWhere('tenat_id',  $this->GetLoginUser()->tenant_id);
						})
						->where(function($query) use (&$search){
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('name', 'like', '%'.$val.'%');break;
								case 'stat':
									$query->where('status', '=' , $val);break;
							}
						}
					})
				->select('*')
				->orderBy('id', 'desc')
				->paginate(10);
		$list->appends($search)->links();
		return $this->response->array($list->toArray());
    }
	public function ListMaterialsAll()
    {
		$list = Materials::where('tenat_id',0)
				->orWhere('tenat_id',  $this->GetLoginUser()->tenant_id)
				->select('id','name','density','price')
				->orderBy('id', 'desc')
				->get();
		return $this->response->array($list->toArray());
    }
	public function FastOrderListMaterialsAll()
    {
		$list = Materials::where('tenat_id',0)
				->select('id','name','density','price')
				->orderBy('id', 'desc')
				->get();
		return $this->response->array($list->toArray());
    }
	public function DelInform(Request $request)
    {
		$this->validate($request, [
			'type' => [
				'required',
				Rule::in(['material', 'molding', 'surface','equipment']),
			],
			'did' => 'required|numeric|max:255',
		]);
		switch($request->type){
			case 'material':
				Materials::destroy($request->did);break;
			case 'molding':
				Molding::destroy($request->did);break;
			case 'surface':
				Surfaces::destroy($request->did);break;
			case 'equipment':
				Equipment::destroy($request->did);break;
		}
		return $this->response->array(['message'=>'ok']);
    }
	//添加成型工艺
	public function AddMolding(Request $request)
    {
			$this->validate($request, ['name' => 'required|unique:molding_processes|max:255']);

			$molding = new Molding;
			$molding->name = $request->name;
			$molding->tenat_id = $this->GetLoginUser()->tenant_id;
			$molding->status =$request->status ? $request->status : 0;
			$molding->save();
	
			return $this->response->array(['message'=>'ok']);
    }
	//编辑成型工艺
	public function EditMolding($id,Request $request)
    {
		$molding = new Molding;
		$mts = $molding::findOrFail($id);
		$mt = $mts->toArray();
		unset($mt['created_at']);
		unset($mt['updated_at']);
		if(empty($request->submit)){
			return $this->response->array($mt);
		}
		else{
			$uniq='';
			if($mts->name != $request->name)$uniq="unique:molding_processes|";
			$this->validate($request, ['name' => 'required|'.$uniq.'max:255']);
			
			$mts->name = $request->name;
			$mts->status =$request->status ? $request->status : 0;
			$mts->save();
	
			return $this->response->array(['message'=>'ok']);
		}
    }
	public function ListMolding(Request $request)
    {
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		
		$list = Molding::where(function ($query) {
							$query->where('tenat_id',0)
							->orWhere('tenat_id',  $this->GetLoginUser()->tenant_id);
						})
						->where(function($query) use (&$search){
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('name', 'like', '%'.$val.'%');break;
								case 'stat':
									$query->where('status', '=' , $val);break;
							}
						}
					})
				->select('*')
				->orderBy('id', 'desc')
				->paginate(10);
		$list->appends($search)->links();
		return $this->response->array($list->toArray());
    }
	public function ListMoldingAll()
    {
		$list = Molding::where('tenat_id',0)
				->orWhere('tenat_id',  $this->GetLoginUser()->tenant_id)
				->select('id','name')
				->orderBy('id', 'desc')
				->get();
		return $this->response->array($list->toArray());
    }
	//添加表面处理
	public function AddSurface(Request $request)
    {
			$this->validate($request, ['name' => 'required|unique:surfaces|max:255']);

			$surface = new Surfaces;
			$surface->name = $request->name;
			$surface->tenat_id = $this->GetLoginUser()->tenant_id;
			$surface->status =$request->status ? $request->status : 0;
			$surface->save();
	
			return $this->response->array(['message'=>'ok']);
    }
	//编辑表面处理
	public function EditSurface($id,Request $request)
    {
		$surface = new Surfaces;
		$mts = $surface::findOrFail($id);
		$mt = $mts->toArray();
		unset($mt['created_at']);
		unset($mt['updated_at']);
		if(empty($request->submit)){
			return $this->response->array($mt);
		}
		else{
			$uniq='';
			if($mts->name != $request->name)$uniq="unique:surfaces|";
			$this->validate($request, ['name' => 'required|'.$uniq.'max:255']);
			
			$mts->name = $request->name;
			$mts->status =$request->status ? $request->status : 0;
			$mts->save();
	
			return $this->response->array(['message'=>'ok']);
		}
    }
	public function ListSurface(Request $request)
    {
        $search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		
		$list = Surfaces::where(function ($query) {
							$query->where('tenat_id',0)
							->orWhere('tenat_id',  $this->GetLoginUser()->tenant_id);
						})
						->where(function($query) use (&$search){
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('name', 'like', '%'.$val.'%');break;
								case 'stat':
									$query->where('status', '=' , $val);break;
							}
						}
					})
				->select('*')
				->orderBy('id', 'desc')
				->paginate(10);
		$list->appends($search)->links();
		return $this->response->array($list->toArray());
    }
	public function ListSurfaceAll(Request $request)
    {
		$list = Surfaces::where('tenat_id',  $this->GetLoginUser()->tenant_id)
				->orWhere('tenat_id',0)
				->select('id','name')
				->orderBy('id', 'desc')
				->get();
		return $this->response->array($list->toArray());
    }
	//添加成型设备
	public function AddEquipment(Request $request)
    {
			$this->validate($request, [
				'mname' => 'required|unique:equipments|max:255',
				'marc' => 'required|max:255',
				'mmaker' => 'required|max:255',
				'mold_id' => 'required|numeric|max:255',
			]);

			$equipments = new Equipment;
			$equipments->mname = $request->mname;
			$equipments->marc = $request->marc;
			$equipments->mmaker = $request->mmaker;
			$equipments->tenat_id = $this->GetLoginUser()->tenant_id;
			$equipments->mold_id = $request->mold_id;
			$equipments->status =$request->status ? $request->status : 0;
			$equipments->save();
	
			return $this->response->array(['message'=>'ok']);
    }
	//编辑成型设备
	public function EditEquipment($id,Request $request)
    {
		$equipments = new Equipment;
		$mts = $equipments::findOrFail($id);
		$mt = $mts->toArray();
		unset($mt['created_at']);
		unset($mt['updated_at']);
		if(empty($request->submit)){
			return $this->response->array($mt);
		}
		else{
			$uniq='';
			if($mts->mname != $request->mname)$uniq="unique:equipments|";
			$this->validate($request, [
				'mname' => 'required|'.$uniq.'max:255',
				'marc' => 'required|max:255',
				'mmaker' => 'required|max:255',
				'mold_id' => 'required|numeric|max:255',
			]);
			$mts->mname = $request->mname;
			$mts->marc = $request->marc;
			$mts->mmaker = $request->mmaker;
			$mts->mold_id = $request->mold_id;
			$mts->status =$request->status ? $request->status : 0;
			$mts->save();
	
			return $this->response->array(['message'=>'ok']);
		}
    }
	public function ListEquipment(Request $request)
    {
        $search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		
		$list = Equipment::where(function ($query) {
							$query->where('tenat_id',0)
							->orWhere('tenat_id',  $this->GetLoginUser()->tenant_id);
						})
						->where(function($query) use (&$search){
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('name', 'like', '%'.$val.'%');break;
								case 'stat':
									$query->where('status', '=' , $val);break;
							}
						}
					})
				->select('*')
				->orderBy('id', 'desc')
				->paginate(10);
		$list->appends($search)->links();
		return $this->response->array($list->toArray());
    }
	public function ListEquipmentAll(Request $request)
    {
		$list = Equipment::where('tenat_id',  $this->GetLoginUser()->tenant_id)
				->orWhere('tenat_id',0)
				->select('id','mname')
				->orderBy('id', 'desc')
				->get();
		return $this->response->array($list->toArray());
    }
}
