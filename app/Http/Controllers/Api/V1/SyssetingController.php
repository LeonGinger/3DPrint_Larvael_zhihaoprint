<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\TenantLevel;
use App\Models\TenantAd;
use App\Models\PlantFormSetting;
use App\Models\TenantTemp;
use App\Models\UserRole;
use App\Models\TenantType;
use App\Models\Department;
use App\Models\User;
use App\Models\Payorder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Http\Requests\Api\V1\SigninRequest;
use App\Http\Transformers\TenantTransformer;
use Carbon\Carbon;
use EasyWeChat\Factory;

class SyssetingController extends Controller
{
    private $guard = 'tenant';

    public function tenantLogin(SigninRequest $request)
    {
        $credentials = $request->only('phone', 'password');
        if (!$token = \Auth::guard($this->guard)->attempt($credentials)) {
            return $this->response->errorUnauthorized('手机号或密码错误');
        }
        $tenant = \Auth::guard($this->guard)->getUser();
        if ($tenant->status == Tenant::STATUS_INACTIVE) {
            return $this->response->errorUnauthorized('该商户已经被禁用');
        }
        return $this->response->item($tenant, new TenantTransformer())
            ->setMeta([
                'access_token' => \Auth::guard($this->guard)->fromUser($tenant),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard($this->guard)->factory()->getTTL() * 60
            ]);
    }
	
	public function GetTenantType()
    {
		$tt = TenantType::select('id','name')->get();
		return $this->response->array($tt->toArray());
    }
	public function CheckTenantInfo()
    {
		$tenid= $this->GetLoginUser()->tenant_id;
		$out = Tenant::where('id',$tenid)->select('name','daihao','linkman','phone','province','city','area','address','tenant_type_id')->first();
		$flt = array_flatten($out->toArray());
		if(in_array(null, $flt))return $this->response->errorUnauthorized('请先完善资料');
		return $this->response->array(['message'=>'资料完整']);
		//return $this->response->array($flt);
    }
    public function GetTenant()
    {
		$tenid= $this->GetLoginUser()->tenant_id;
		$out = Tenant::where('id',$tenid)->select('name','daihao','linkman','phone','province','city','area','address','tenant_type_id')->first();
		return $this->response->array($out->toArray());
    }
	public function SetTenant(Request $request)
    {
		$tenid= $this->GetLoginUser()->tenant_id;
		$pho = Tenant::where('id',$tenid)->value('phone');
		$pho = $pho==$request->phone ? '' : 'unique:tenants|';
		
		$this->validate($request, [
			'name' => 'required',
			'daihao' => 'required',
			'linkman' => 'required',
			'phone' => 'required|'.$pho.'regex:/^1[34578][0-9]{9}$/',
		]);
		$parm = array();
		$parm['name'] = $request->name;
		$parm['daihao'] = $request->daihao;
		$parm['linkman'] = $request->linkman;
		$parm['phone'] = $request->phone;
		$parm['tenant_type_id'] = $request->tenant_type_id;
		$request->province && $parm['province'] = $request->province;
		$request->city && $parm['city'] = $request->city;
		$request->area && $parm['area'] = $request->area;
		$request->address && $parm['address'] = $request->address;
		
		Tenant::where('id',$tenid)->update($parm);
		User::where('phone',$request->phone)->update(['name'=>$request->linkman]);
		return $this->response->array(array('code'=>200,'success'=>'ok'));
    }
	public function TenantLevel(){
		$tenid= $this->GetLoginUser()->tenant_id;
		$tli = Tenant::where('tenants.id',$tenid)
						->join('tenant_levels','tenant_levels.id','=','tenants.tenant_level_id')
						->select('tenant_levels.name','tenant_levels.customer_numbers','tenant_levels.order_numbers','tenants.expired_at')
						->first();
		if($tli){
			return $this->response->array($tli->toArray());
		}else{
			$out = array(
				'name' => '免费用户',
				'customer_numbers' => '5',
				'order_numbers ' => '20',
				'expired_at ' => null,
			);
			return $this->response->array($out);
		}
	}
	public function TenantLevelList(){
			$tl = TenantLevel::where('name','<>','免费用户')->select('id','name','customer_numbers','order_numbers','quarter_price','year_price')->get();
			return $this->response->array($tl->toArray());
	}
	public function TenantLevelUp(Request $request){
		$this->validate($request, [
			'lid' => 'required',
			'pay_type' => 'required',
		]);
		$tl = TenantLevel::find($request->lid);
		if(empty($tl))return $this->response->errorUnauthorized('找不到这个等级啊，好捉急~');
		$py = new Payorder;
		$py->trade_no = date("YmdHis").rand(1111,9999);
		$py->tenat_id = $this->GetLoginUser()->tenant_id;
		$py->product_id = $tl->id;
		$py->pay_type = $request->pay_type;
		switch($request->pay_type){
			case 'quarter_price':
				$py->total_fee = $tl->quarter_price;break;
			case 'year_price':
				$py->total_fee = $tl->year_price;break;
		}
		$py->save();
		$app = Factory::payment(config('wechatpay'));
		$result = $app->order->unify([
			'body' => 'TPM3D盈普生产系统-升级'.$tl->name,
			'out_trade_no' => $py->trade_no,
			//'total_fee' => $py->total_fee * 100,
			'total_fee' =>1,
			'trade_type' => 'NATIVE',
			'product_id' => $py->product_id,
		]);
		//dd($result);
		if(!file_exists(public_path('qrcodes')))mkdir(public_path('qrcodes'));
		$filename = $py->trade_no.'.png';
		$qurul = env('APP_URL').'/qrcodes/'.$filename;
		\QrCode::format('png')->size(200)->margin(0)->generate($result['code_url'],public_path('qrcodes/'.$filename));
		
		return $this->response->array(['qrurl'=>$qurul,'trade_no'=>$py->trade_no]);
	}
	public function OrderQuery(Request $request){
		$this->validate($request, ['trade_no' => 'required']);
		$py = Payorder::where('trade_no',$request->trade_no)->first();
		$out = array();
		if(!empty($py)){
			if($py->status == 'paid'){
				$out['return_code']='SUCCESS';
				$out['return_msg']='支付完成';
			}else{
				$out['return_code']='FAIL';
				$out['return_msg']='未支付或支付失败';
			}
		}else{
			$out['return_code']='FAIL';
			$out['return_msg']='找不到订单';
		}
		return $this->response->array($out);
	}
	public function WechatPayOrderList(Request $request)
    {
		$search = array();
			$request->get('sear') && $search['sear'] = $request->get('sear');
			$request->get('orde') && $search['orde'] = $request->get('orde');
			$request->get('edat') && $search['edat'] = $request->get('edat');
			$request->get('type') && $search['type'] = $request->get('type');
			if($request->get('stat') !== null) $search['stat'] = $request->get('stat');
			
        $orders = Payorder::where(function($query) use (&$search){
						$query->where('payorders.tenat_id', $this->GetLoginUser()->tenant_id);
						foreach($search as $key => $val){
							switch($key){
								case 'sear':
									$query->where('tenant_levels.name', 'like', '%'.$val.'%');break;
								case 'orde':
									$query->where('payorders.trade_no', '=', $val);break;
								case 'type':
									$query->where('payorders.pay_type', '=', $val);break;
								case 'stat':
									if($val == 0)break;//所有订单
									if($val == 1)$query->where('payorders.status', '=', null);//未支付
									if($val == 2)$query->where('payorders.status', '=', 'paid');//已支付
									if($val == 3)$query->where('payorders.status', '=', 'paid_fail');//支付失败
									break;
								case 'edat':
									$query->whereDate('payorders.paid_at', '=' , $val);break;
							}
						}
					})
					->join('tenant_levels', 'tenant_levels.id', '=', 'payorders.product_id')
					->leftJoin('invoices', 'invoices.payid', '=', 'payorders.trade_no')
					->select('payorders.id','payorders.trade_no','tenant_levels.name as product_name','payorders.pay_type','payorders.total_fee','payorders.status','payorders.paid_at','payorders.created_at','invoices.status as invstatus','invoices.file_url')
					->orderBy('payorders.id', 'desc')
					->paginate(10);
		$orders->appends($search)->links();
		
		return $this->response->array($orders);
    }
	public function AdUpdate(Request $request)
    {
		$this->validate($request, [
			'page' => [
				'required',
				Rule::in(['报价单', '合同', '送货单']),
			],
			'loca' => [
				'required',
				Rule::in(['foot', 'top']),
			],
			'file' => 'required',
		]);
		$tenid= $this->GetLoginUser()->tenant_id;
		$tent = Tenant::find($tenid);
		$tentlev = TenantLevel::find($tent->tenant_level_id);
		if($tentlev->name == '免费用户')return $this->response->errorUnauthorized('您是免费用户，不能修改广告，请先升级至VIP。');
        $path = $request->file('file')->store('public/'.date('Ymd'));
		$url = env('APP_URL').Storage::url($path);
		$tat = TenantAd::where('tenant_id',$tenid)->where('page',$request->page)->where('loca',$request->loca)->value('id');
		if(empty($tat)){
			$ta = new TenantAd;
			$ta->tenant_id=$tenid;
			$ta->page=$request->page;
			$ta->loca=$request->loca;
			$ta->imgurl=$url;
			$ta->save();
		}else{
			TenantAd::where('id',$tat)->update(['imgurl' => $url]);
		}
		return $this->response->array($url);
    }
	public function AdGet(Request $request)
    {
			
		$this->validate($request, [
			'page' => [
				'required',
				Rule::in(['报价单', '合同', '送货单']),
			],
		]);
		$tenid= $this->GetLoginUser()->tenant_id;
		$out = array();
		$tat = TenantAd::where('tenant_id',$tenid)->where('page',$request->page)->select('loca','imgurl')->get();
		
        if(empty($tat->toArray())){
			$pfs = PlantFormSetting::first();
			$out['top'] = $pfs->ad_header_url;
			$out['foot'] = $pfs->ad_footer_url;
		}else{
			foreach($tat as $item){
				$key = $item['loca'];
				$out[$key] = $item['imgurl'];
			}
			if(count($out) != 2){
				$pfs = PlantFormSetting::first();
				!array_key_exists("top",$out) && $out['top'] = $pfs->ad_header_url;
				!array_key_exists("foot",$out) && $out['foot'] = $pfs->ad_footer_url;
			}
			
		}

        return $this->response->array($out);
    }
	public function TenantTemp(Request $request)
    {
		$this->validate($request, [
			'text' => [
				'required',
				Rule::in(['text1', 'text2']),
			],
		]);
		$tenid= $this->GetLoginUser()->tenant_id;
		if(empty($request->valu)){
			$out = '';
			$tp = TenantTemp::where('tenant_id',$tenid)->first();
			
			if(empty($tp)){
				$pfs = PlantFormSetting::first();
				switch($request->text){
					case 'text1':
						$out= $pfs->baojia_template_text1;
						break;
					case 'text2':
						$out= $pfs->baojia_template_text2;
						break;
				}
			}else{
				$key = $tp->toArray();
				$out = $key[$request->text];
				if(empty($out)){
					switch($request->text){
						case 'text1':
							$key = 'baojia_template_text1';
							break;
						case 'text2':
							$key = 'baojia_template_text2';
							break;
					}
					$out = PlantFormSetting::value($key);
				}
			}
		}else{
			$tp = TenantTemp::where('tenant_id',$tenid)->first();
			if(empty($tp)){
				$pp = new TenantTemp([
						'tenant_id' => $tenid,
						$request->text => $request->valu,
					]);
				$pp->save();
			}else
				TenantTemp::where('tenant_id',$tenid)->update([$request->text => $request->valu]);
			$out = $request->valu;
		}
        return $this->response->array($out);
    }
	//查询登录商家是否过期
	public function TenantExpired(Request $request)
    {
		$out = array();
		$tenex = Tenant::where('id',$this->GetLoginUser()->tenant_id)->value('expired_at');
		if(empty($tenex)){
			$out['exday'] = 0;
			$out['date'] = '免费用户';
			$out['status'] = '免费用户';
			$out['code'] = 3;
			return $this->response->array($out);
		}
		$exdate = Carbon::parse($tenex);
		$today = now();
		$exday = $exdate->diffInDays($today,false);
		
		$out['exday'] = $exday;
		if($exday > 0 ){
			$out['date'] = $exdate->diffForHumans($today,true);
			$out['status'] = '已过期';
			$out['code'] = 1;
		}elseif($exday == 0){
			if($today < $exdate){
				$out['date'] = $exdate->diffForHumans($today);
				$out['status'] = '未过期';
				$out['code'] = 0;
			}else{
				$out['date'] = $exdate->diffForHumans($today,true);
				$out['status'] = '已过期';
				$out['code'] = 1;
			}
		}else{
			$out['date'] = $exdate->diffForHumans($today);
			$out['status'] = '未过期';
			$out['code'] = 0;
		}
		return $this->response->array($out);
	}
}
