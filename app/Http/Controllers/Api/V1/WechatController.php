<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Linkman;
use App\Models\User;
use App\Models\Materials;
use App\Models\Equipment;
use App\Models\Order;
use App\Models\Part;
use App\Models\Quotation;
use App\Models\Tenant;
use App\Models\PlantFormSetting;
use App\Models\TenantTemp;
use App\Models\Wechatbind;
use App\Models\Evaluate;
use App\Models\ReturnNote;
use App\Models\Changelog;
use App\Http\Transformers\UserTransformer;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;
use Overtrue\Pinyin\Pinyin;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\View;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Hash;
use TCPDF;

class WechatController extends Controller
{
	private $guard = 'api';
    //前端确定报价单
	public function QuotationConfirm(Request $request){
		
		$this->validate($request, [
			'val' => 'required',
		]);
		try {
			$vt = urldecode($request->Input('val'));
			$oid = decrypt($vt);
		} catch (DecryptException $e) {
			return $this->response->errorUnauthorized('报价单错误');
		}
		Order::where('id', $oid)->update(['status' => 2]);
		$qut = Quotation::where('order_id', $oid)->first();
		$qut->status = 2;
		$qut->save();

		$order = Order::find($oid);
		if(empty($order))return $this->response->errorUnauthorized('无订单信息');
		$quot = Quotation::where('order_id', $oid)->first();
		if(empty($quot))return $this->response->errorUnauthorized('报价单错误');
		$cus = Customer::where('id', $order->customer_id)->select('name','ticket_info')->first();
		$lks = explode(',', $order->lkmans);
		$lks = Linkman::whereIn('id', $lks)->select('*')->get();
		
		$parts = Part::whereIn('parts.id', json_decode($order->parts,true))
						->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
						->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
						->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
						->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
						->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname')
						->get();
		
		$out = $quot->toArray();
		$out['cusname'] = $cus->name;
		$out['ticket_info'] = unserialize($cus->ticket_info);
		$out['linkmans'] = $lks->toArray();
		$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
		$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
		$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
		$out['parts'] = $parts;
		$out['taxation'] = $order->taxation;
		$out['freight'] = $order->freight;
		
			$url = 'https://yp-dev.one2fit.cn/admin/#/modules/index';
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->saler),1,2,$order->no,$url);
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->pojer),1,2,$order->no,$url);
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$order->manger),1,2,$order->no,$url);
			
			$this->SendWechatMessage(array('utype'=>'customer','uid'=>$order->customer_id),1,26,$order->no,url('/customer/orderlist'));
		
		//$rnd = date("Ymdhis").rand(1111,9999);
		//$rot = 'contract/'.$rnd.'.pdf';
		//if (View::exists('wechat.contract')) {
			//$view = View::make('wechat.contract')->with('data',$out);
			//$html = response($view)->getContent();
			//return $this->response->array($html);
			//PDF::loadHTML($html)->setPaper('a4')->save('eliverynote.pdf');
			//PDF::loadHTML($html)->setPaper('a4')->setWarnings(false)->save($rot);
			//return $this->response->array($html);
		//}
		
		//Order::where('id', $oid)->update(['contract' =>  env('APP_URL').'/'.$rot]);
		Order::where('id', $oid)->update(['contract' =>  env('APP_URL').'/wechat/order/contract?val='.urldecode($request->Input('val'))]);
		
		return $this->response->array(array('code'=>200,'message'=>'ok'));
	}
	//微信登陆
	public function WechatLogin(Request $request){
		$this->validate($request, [
			'oid' => 'required',
		]);
		$tenant = User::where('openid',$request->oid)->first();
		if(empty($tenant))return $this->response->errorUnauthorized('未绑定微信');
        if ($tenant->status == Tenant::STATUS_INACTIVE) {
            return $this->response->errorUnauthorized('该商户已经被禁用');
        }
        return $this->response->item($tenant, new UserTransformer())
            ->setMeta([
                'access_token' => \Auth::guard($this->guard)->fromUser($tenant),
                'token_type' => 'Bearer',
                'expires_in' => \Auth::guard($this->guard)->factory()->getTTL() * 60
            ]);
	}
	//用户绑定微信
	public function WechatBind(Request $request){
		$this->validate($request, [
			'uid' => 'required',
		]);
		$wuser = new Wechatbind;
		$wuser->uid = $request->uid;
		$wuser->type = 'bind';
		$wuser->save();
		//$qrcode = QrCode::size(288)->generate('https://yp-dev.one2fit.cn/wechatbind?val='.encrypt('88888888|'.$wuser->id));
		//return $this->response->array(['message'=>$qrcode]);
		$app = Factory::officialAccount(config('wechat'));
		$result = $app->qrcode->temporary($wuser->id, 10 * 60);
		$url = $app->qrcode->url($result['ticket']);
		return $this->response->array(['message'=>$url]);
	}
	//用户解除绑定微信
	public function WechatUntying(Request $request){
		$this->validate($request, [
			'uid' => 'required',
		]);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$request->uid),1,30,'');
		User::where('id',$request->uid)->update(['openid' => null]);
		return $this->response->array(['message'=>'解绑成功！']);
	}
	public function WechatGetid(Request $request){
		$this->validate($request, [
			'code' => 'required',
		]);
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wx3b08c8e3c237b6b0&secret=f8209e8fa58f882f2f3e770cabc09928&code='.$request->code.'&grant_type=authorization_code';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$output = curl_exec($ch);
		curl_close($ch);
		$jsary = json_decode($output,true);
		if(!empty($jsary['openid']))
			return $this->response->array($jsary['openid']);
		else
			return $this->response->errorUnauthorized('code已过期');
	}
	//用户修改报价单
	public function QuotationEdit(Request $request){
		$oid = urldecode($request->Input('val'));
		
		try {
			$oid = decrypt($oid);
		} catch (DecryptException $e) {
			return $this->response->errorUnauthorized('错误');
		}
		$logtext = '';
		foreach($request->parts as $key=>$val){
			$part = Part::find($key);
			$uparray = array();
			if($part->product_num != $val['product_num']){
				$uparray['product_num'] = $val['product_num'];
				$logtext .= date('y/m/d').' 客户修改了“'.$part['name'].'”数量 为'.$val['product_num']."\r\n";
			}
			if($part->remark != $val['remark']){
				$uparray['remark'] = $val['remark'];
				$logtext .= date('y/m/d').' 客户修改了“'.$part['name'].'”备注 为'.$val['remark']."\r\n";
			}
			if(!empty($uparray))Part::where('id',$key)->update($uparray);
			$uparray['part_id'] = $key;
			Changelog::updateOrCreate(['part_id' => $key],$uparray);
		}
		
		Order::where('id', $oid)->update(['status'=>0]);
		$quo = Quotation::where('order_id', $oid)->first();
		$quo->status = 0;
		$quo->total = $request->total;
		$quo->remark = $request->remark;
		$quo->log = $quo->log . $logtext;
		$quo->save();
			$cusid = Order::where('id',$oid)->select('id','no','saler','pojer','manger')->first();
			$url = 'https://yp-dev.one2fit.cn/admin/#/modules/index';
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$cusid->saler),1,7,$cusid->no,$url);
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$cusid->pojer),1,7,$cusid->no,$url);
			$this->SendWechatMessage(array('utype'=>'system','uid'=>$cusid->manger),1,7,$cusid->no,$url);
		return $this->response->array(['message'=>'ok']);
	}
	//商家入驻
	public function Register(Request $request){
		$this->validate($request, [
			'vk' => 'required',
			'vo' => 'required|unique:tenants,weapp_openid',
			'vp' => 'required',
			'phone' => 'required|unique:tenants|regex:/^1[34578][0-9]{9}$/',
		]);
		
		$verifyData = \Cache::get($request->vk);
        if (!$verifyData)return $this->response->errorUnauthorized('验证码失效，请重新获取');
        if (!hash_equals($verifyData['code'], $request->code))return $this->response->errorUnauthorized('验证码错误');
        \Cache::forget($request->vk);
		
		$tt = new Tenant;
		$tt->phone = $request->phone;
		$tt->password =  Hash::make($request->vp);
		$tt->tenant_level_id = 1;
		$tt->weapp_openid = $request->vo;
		$tt->status = 1;
		$tt->save();
		
		$sele = str_random(6);
		//添加管理员信息
		$user = new User;
		$user->name = "超级管理员";
		$user->tenant_id = $tt->id;
		$user->dep_ids = 0;
		$user->pr_id = 0;
		$user->password = Hash::make($request->vp);
		$user->sale = $sele;
		$user->openid = $request->vo;
		$user->phone = $request->phone;
		$user->status = 1;
		$user->save();
		return $this->response->array(['message'=>'ok']);
	}
	//商家入驻
	public function tcpdf(){
		//pathinfo("/testweb/test.txt")
		//$out = str_after("https://yp-dev.one2fit.cn/uploads/images/plantform_settings/ad_header_image/201812/21/_1545360654_QOh5wG3BhJ.jpg",env('APP_URL'));
			$out = str_replace_first(env('APP_URL').'/storage/', '../../../../../storage/app/public/', 'https://yp-dev.one2fit.cn/storage/20181130/NLO1HRYMbgH5o7LhbPb24jw9L6fADRz0sVbxPjXj.jpeg');
		//return $this->response->array(['message'=>$_SERVER['DOCUMENT_ROOT'].'/uploads/images/plantform_settings/ad_header_image/201812/21/_1545360654_QOh5wG3BhJ.jpg']);
		return $this->response->array(['message'=>$out]);
	}
	//发表评价
	public function SendEvaluate(Request $request){
		$this->validate($request, [
			'content' => 'required',
			'pingjia' => 'required',
			'manger_star' => 'required',
			'pojer_star' => 'required',
			'saler_star' => 'required',
			'oid' => 'required',
		]);
		$oid = '';
		try{
			$vt = urldecode($request->oid);
			$oid = decrypt($vt);
		}catch(DecryptException $e) {
			return $this->response->errorUnauthorized('订单号错误');
		}
		$ord = Order::find($oid);
		if(empty($ord))return $this->response->errorUnauthorized('无订单信息');
		$eva = new Evaluate;
		$eva->tenat_id = $ord->tenat_id;
		$eva->customer_id = $ord->customer_id;
		$eva->order_id = $ord->id;
		$eva->manger = $ord->manger;
		$eva->pojer = $ord->pojer;
		$eva->saler = $ord->saler;
		$eva->content = $request->content;
		$eva->pingjia = $request->pingjia;
		$eva->manger_star = $request->manger_star;
		$eva->pojer_star = $request->pojer_star;
		$eva->saler_star = $request->saler_star;
		$eva->anonymous = empty($request->anonymous)? 0 : 1;
		$eva->save();
		
		$url = url('/wechat/admin/evaluate').'?val='.urldecode($request->oid);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->saler),1,29,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->pojer),1,29,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->manger),1,29,$ord->no,$url);
		return $this->response->array(['message'=>'ok']);
	}
	//申请退货
	public function OrderReturn(Request $request){
		$this->validate($request, [
			'val' => 'required',
		]);
		try {
			$vt = urldecode($request->Input('val'));
			$oid = decrypt($vt);
		} catch (DecryptException $e) {
			return $this->response->errorUnauthorized('报价单错误');
		}
		$ord = Order::find($oid);
		if(empty($ord))return $this->response->errorUnauthorized('无订单信息');
		$ren = new ReturnNote;
		$ren->order_id = $ord->id;
		$ren->tenat_id = $ord->tenat_id;
		$ren->commit_user_id = $ord->customer_id;
		$ren->handle_user_id = $ord->saler;
		$ren->returned_type = empty($request->retyp)? 0 : $request->retyp;
		$ren->remark = empty($request->remark)? null : $request->remark;
		$ren->status = empty($request->status)? 0 : $request->status;
		$ren->save();
		$url = 'https://yp-dev.one2fit.cn/admin/#/modules/index';
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->saler),1,15,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->pojer),1,15,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->manger),1,15,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'customer','uid'=>$ord->customer_id),1,14,$ord->no,url('/wechat/order/aftersale').'?val='.encrypt($ord->id));
		return $this->response->array(['message'=>'提交成功！']);
	}
	//取消退货
	public function OrderReturnCancel(Request $request){
		$this->validate($request, [
			'val' => 'required',
		]);
		try {
			$vt = urldecode($request->Input('val'));
			$temp = explode('|', decrypt($vt));
			$oid = $temp[0];
		} catch (DecryptException $e) {
			return $this->response->errorUnauthorized('报价单错误');
		}
		$rnot = ReturnNote::find($oid);
		if($rnot->status)return $this->response->errorUnauthorized('此申请已经通过审核，不能撤销了。');
		ReturnNote::where('id',$oid)->delete();
		return $this->response->array(['message'=>'撤销成功']);
	}
	//申请零件重做
	public function OrderReturnRebuid(Request $request){
		$this->validate($request, [
			'val' => 'required',
		]);
		$oid = urldecode($request->Input('val'));
		try {
			$oid = decrypt($oid);
		} catch (DecryptException $e) {
			return $this->response->errorUnauthorized('报价单错误');
		}
		//$parts = array();
		foreach($request->pid as $key=>$val){
			//if(!empty($val) && (int)$val){
			if(0){
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
		$ord = Order::find($oid);
		if(empty($ord))return $this->response->errorUnauthorized('无订单信息');
		$ren = new ReturnNote;
		$ren->order_id = $ord->id;
		$ren->tenat_id = $ord->tenat_id;
		$ren->parts = json_encode($request->pid, JSON_NUMERIC_CHECK);
		$ren->commit_user_id = $ord->customer_id;
		$ren->handle_user_id = $ord->saler;
		$ren->returned_type = 1;
		$ren->remark = empty($request->remark)? null : $request->remark;
		$ren->status = empty($request->status)? 0 : $request->status;
		$ren->save();
		$url = 'https://yp-dev.one2fit.cn/admin/#/modules/index';
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->saler),1,24,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->pojer),1,24,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->manger),1,24,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'customer','uid'=>$ord->customer_id),1,25,$ord->no,url('/wechat/order/aftersale').'?val='.encrypt($ord->id));
		return $this->response->array(['message'=>'提交成功！']);
	}
	//取消重做
	public function OrderReturnCanRebuid(Request $request){
		$this->validate($request, [
			'val' => 'required',
		]);
		try {
			$vt = urldecode($request->Input('val'));
			$oid = decrypt($vt);
		} catch (DecryptException $e) {
			return $this->response->errorUnauthorized('报价单错误');
		}
		ReturnNote::where('order_id',$oid)->delete();
		return $this->response->array(['message'=>'撤销成功']);
	}
	//申请再次下单
	public function OrderReload(Request $request){
		$this->validate($request, [
			'val' => 'required',
		]);
		$havepart = 0;
		foreach($request->pid as $val){
			$havepart += (int)$val;
		}
		if(!$havepart)$this->response->errorUnauthorized('没有添加零件');
		$oid = urldecode($request->Input('val'));
		try {
			$oid = decrypt($oid);
		} catch (DecryptException $e) {
			return $this->response->errorUnauthorized('报价单错误');
		}
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
		$cid = $nod->id;
		$tolicon = $ord->taxation + $ord->freight;
		$oldquot = Quotation::where('order_id',$oid)->first();
		
		$parts = array();
		foreach($request->pid as $key=>$val){
			if(!empty($val) && (int)$val){
				$tp = Part::find($key);
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
				$pa->product_num = $val;
				$pa->start_date = now();
				$pa->due_date = now();
				$pa->save();
				array_push($parts,$pa->id);
				$tolicon = $tolicon + $tp->coefficient * $tp->price * $val;
			}
		}
		Order::where('id', $cid)->update(['parts' => json_encode($parts, JSON_NUMERIC_CHECK)]);
		
		//添加报价单
		if(!file_exists(public_path('qrcodes')))
			mkdir(public_path('qrcodes'));
		$filename = date("YmdHis").rand(1111,9999).'.png';
		$qurul = env('APP_URL').'/qrcodes/'.$filename;
		QrCode::format('png')->size(488)->generate('https://yp-dev.one2fit.cn/quotation?val='.encrypt($cid),public_path('qrcodes/'.$filename));
		$jc = 'CUSTOMER';
		$quotno = date("Ymd").'-'.Customer::where('id', $ord->customer_id)->value('no').'-'.Tenant::where('id', $ord->tenat_id)->value('daihao').'-'.$jc;

		
		$quot = new Quotation;
		$quot->order_id = $cid;
		$quot->no = $quotno;
		$quot->qrcode_url = $qurul;
		$quot->qs = $oldquot->qs;
		$quot->qt = $oldquot->qt;
		$quot->total = $tolicon;
		$quot->save();
			
		$url = 'https://yp-dev.one2fit.cn/admin/#/modules/index';
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->saler),1,27,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->pojer),1,27,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'system','uid'=>$ord->manger),1,27,$ord->no,$url);
		$this->SendWechatMessage(array('utype'=>'customer','uid'=>$ord->customer_id),1,28,$ord->no,'https://yp-dev.one2fit.cn/quotation?val='.encrypt($cid));
		return $this->response->array(['message'=>'下单成功！']);
	}
}
