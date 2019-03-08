<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\UserRole;
use Illuminate\Validation\Rule;
use App\Http\Middleware\UserRoleAction;
use App\Http\Transformers\UserTransformer;
use Illuminate\Support\Facades\Hash;
use EasyWeChat\Factory;


class UsersController extends Controller
{
    //添加部门用户
	public function AddUser(Request $request)
    {

		$sele = str_random(6);
		$this->validate($request, [
			'name' => 'required|max:255',
			'phone' => 'required|unique:users|regex:/^1[34578][0-9]{9}$/',
			'password' => 'required',
			'dep_ids' => 'required',
			'pr_id' => 'required'
		]);
		//添加用户
		$user = new User;
		$user->name = $request->name;
		$user->tenant_id = $this->GetLoginUser()->tenant_id;
		$user->dep_ids = $request->dep_ids;
		$user->pr_id = $request->pr_id;
		$user->password = Hash::make($request->password);
		$user->sale = $sele;
		$user->phone = $request->phone;
		$user->save();
		
		return $this->response->array(array('mssage'=>'操作成功'));

    }
	//编辑部门用户
	public function EditUser($id,Request $request)
    {
		$user = User::find($id);
		if(empty($request->submit)){
			return $this->response->item($user, new UserTransformer());
		}else{
			$prlt = ($user->phone == $request->phone)? '' : 'unique:users|';
			$this->validate($request, [
				'name' => 'required|max:255',
				'phone' => 'required|'.$prlt.'regex:/^1[34578][0-9]{9}$/',
				'dep_ids' => 'required',
				'pr_id' => 'required',
				'status' => 'required',
			]);
			//添加用户
			$user->name = $request->name;
			$user->dep_ids = $request->dep_ids;
			$user->pr_id = $request->pr_id;
			if(!empty($request->password))
				$user->password = Hash::make($request->password);
			$user->phone = $request->phone;
			$user->status = $request->status;
			$user->save();
			
			return $this->response->array(array('mssage'=>'操作成功'));
		}
    }
	//删除用户
	public function DeleUser(Request $request){
		$this->validate($request, [
				'duid' => 'required'
			]);
		User::destroy($request->duid);
		return $this->response->array(array('mssage'=>'操作成功'));
	}
	//部门用户列表
	public function ListUser(Request $request)
    {
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('depa') && $search['depa'] = $request->get('depa');
			$request->get('prmi') && $search['prmi'] = $request->get('prmi');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
		$users = User::where(function($query) use (&$search){
						$query->where('users.tenant_id', $this->GetLoginUser()->tenant_id);
						$query->where('users.dep_ids','<>', 0);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('users.name', 'like', '%'.$val.'%');break;
								case 'depa':
									$query->where('users.dep_ids', '=', $val);break;
								case 'prmi':
									$query->where('users.pr_id', '=', $val);break;
								case 'stat':
									$query->where('users.status', '=', $val);break;
							}
						}
					})
						->leftJoin('departments', 'departments.id', '=', 'users.dep_ids')
						->leftJoin('user_roles', 'user_roles.id', '=', 'users.pr_id')
						->select('users.id','users.name','users.phone','departments.depname','user_roles.name as prname','users.openid','users.status')
						->paginate(5);
		$users->appends($search)->links();
		return $this->response->array($users->toArray());
	}
	//部门列表
	public function ListDepartment()
    {
		$depa = Department::select('id','depname','depcode')->get();
		return $this->response->array($depa->toArray());
	}
	//添加权限
	public function AddUserRole(Request $request)
    {
		$this->validate($request, [
			'name' => 'required|unique:user_roles|max:255',
			'depcode' => 'required',
			'action' => 'required',
		]);
		$usr = new UserRole;
		$usr->tenat_id = $this->GetLoginUser()->tenant_id;
		$usr->name = $request->name;
		$usr->depcode = json_encode($request->depcode);
		$usr->action = json_encode($request->action);
		$usr->save();
		return $this->response->array(array('message'=>'添加成功'));
	}
	public function UserRoleList()
	{
		$urle = UserRole::where('tenat_id',$this->GetLoginUser()->tenant_id)->orWhere('tenat_id',0)->select('id','name','depcode','action','tenat_id')->get();
		return $this->response->array($urle->toArray());
	}
	public function UserRoleGet(Request $request)
	{
		$this->validate($request, [
			'rid' => 'required',
		]);
		
		if($request->rid == 0){
			$out = array(
    "id"=>0,
    "name"=> "超级管理员",
    "depcode"=>"[\"admin\",\"info\",\"sale\",\"depo\",\"prod\"]",
    "action"=>"[\"add\",\"edit\",\"del\",\"list\",\"check\",\"delete\"]"
			);
			return $this->response->array($out);
		}
		
		$urle = UserRole::where('id',$request->rid)->select('id','name','depcode','action','tenat_id')->first();
		if(empty($urle))return $this->response->errorUnauthorized('找不到这个名称！');
		if(empty($request->submit)){
			return $this->response->array($urle->toArray());
		}else{
			if($urle->tenat_id != $this->GetLoginUser()->tenant_id)return $this->response->errorUnauthorized('不能修改系统内置权限');
			$nrlt = ($urle->name == $request->name)? '' : 'unique:user_roles|';
			$this->validate($request, [
				'name' => 'required|'.$nrlt.'max:255',
				'depcode' => 'required',
				'action' => 'required',
			]);
			$urle->name = $request->name;
			$urle->depcode = json_encode(array_wrap($request->depcode));
			$urle->action = json_encode(array_wrap($request->action));
			$urle->save();
			return $this->response->array(array('message'=>'修改成功'));
		}
	}
	public function UserRoleDel(Request $request)
	{
		$this->validate($request, [
			'rid' => 'required',
		]);
		$ur = UserRole::find($request->rid);
		if($ur->tenat_id != $this->GetLoginUser()->tenant_id)return $this->response->errorUnauthorized('不能删除系统内置权限');
		$isuse = User::where('pr_id',$request->rid)->first();
		if(empty($isuse)){
			$ur->delete();
			return $this->response->array(['message'=>'删除成功']);
		}else{
			return $this->response->errorUnauthorized('该权限已经分配给用户，不能删除。');
		}
	}
}
