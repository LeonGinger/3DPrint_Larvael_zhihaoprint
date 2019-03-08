<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TenantType;
use App\Models\Tenant;
use EasyWeChat\Factory;
use App\Models\User;
use App\Models\Wechatbind;
use App\Models\Order;
use Overtrue\Pinyin\Pinyin;
use Illuminate\Support\Facades\Hash;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Media;
use Illuminate\Support\Facades\Log;
use App\Models\Payorder;
use Carbon\Carbon;

class WechatController extends Controller
{
	
	public function OauthCallback(){

		$app = Factory::officialAccount(config('wechat'));
		$oauth = $app->oauth;

		// 获取 OAuth 授权结果用户信息
		$user = $oauth->user();

		//$_SESSION['wechat_user'] = $user->toArray();
		session(['wechat_user' => $user->toArray()]);
		$targetUrl = empty(session('target_url')) ? '/customer/orderlist' : session('target_url');

		header('location:'. $targetUrl); // 跳转到 user/profile
	}
	public function Register(Request $request){
		if(!empty($request->Input())){
			$this->validate($request, [
				'compname' => 'required',
				'user' => 'required|max:255',
				'hangye' => 'required',
				'password' => 'required',
				'openid' => 'required',
				'phone' => 'required|unique:users|regex:/^1[34578][0-9]{9}$/',
			]);
			$pinyin = new Pinyin();
			$tt = new Tenant;
			$tt->name = $request->Input('compname');
			$tt->daihao = $pinyin->abbr($request->Input('compname'), PINYIN_KEEP_ENGLISH);
			$tt->linkman = $request->Input('user');
			$tt->phone = $request->Input('phone');
			$tt->password =  Hash::make($request->Input('password'));
			$tt->tenant_type_id = $request->Input('hangye');
			$tt->tenant_level_id = 1;
			$tt->weapp_openid = $request->Input('openid');
			$tt->status = 0;
			$tt->save();
			
			$sele = str_random(6);
			//添加管理员信息
			$user = new User;
			$user->name = $request->Input('user');
			$user->tenant_id = $tt->id;
			$user->dep_ids = 0;
			$user->pr_id = 0;
			$user->password = Hash::make($request->Input('password'));
			$user->sale = $sele;
			$user->openid = $request->Input('openid');
			$user->phone = $request->Input('phone');
			$user->save();
			
			return response('Hello World', 200);
		
		};
		
		$app = Factory::officialAccount(config('wechat'));
		$oauth = $app->oauth;
		// 未登录
		if (empty(session('wechat_user'))) {
			//$cookie = cookie('target_url', 'value', $minutes);
			session(['target_url' => '/wechat/register']);
			return $oauth->redirect();
		}


		// 已经登录过
		$user = session('wechat_user');
		$flight = Tenant::where('weapp_openid', $user['id'])->first();
		//if(!empty($flight)){$data = $app->jssdk->buildConfig(array('closeWindow'), false,false,false);return view('wechat.registered')->with('jssdk', $data);}
		$data = array();
		$data['hangye'] = TenantType::select('id','name')->get();
		$data['user'] = $user;
		$data['jssdk'] = $app->jssdk->buildConfig(array('closeWindow'), false,false,false);
		return view('wechat.register')->with('data', $data);
	}
	//微信回复
	public function Service(Request $request){
		$app = Factory::officialAccount(config('wechat'));
		$app->server->push(function ($message) {
			if($message['MsgType'] == 'text'){
				if($message['Content'] == '我要报价')
					return "<a href='https://yp-dev.one2fit.cn/admin/#/kusubaojia'>快速报价</a>\n\n\n<a href='https://yp-dev.one2fit.cn/wechat/register'>商家入驻</a>\n\n\n<a href='https://yp-dev.one2fit.cn/customer/orderlist'>我的订单（客户）</a>\n\n\n<a href='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx3b08c8e3c237b6b0&redirect_uri=https://yp-dev.one2fit.cn/admin&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect'>商家登录</a>";
			}
			if($message['MsgType'] == 'event'){
				if(empty($message['EventKey']))return "欢迎关注TPM3D盈普微信公众号！";
				$keyArray = explode("_", $message['EventKey']);
				$ekey = count($keyArray) == 1 ? $message['EventKey'] : $keyArray[1];
				
				$wb = Wechatbind::find($ekey);
				if(empty($wb))return "抱歉！二维码已失效。";
				switch($wb->type){
					case 'bind':
						Wechatbind::destroy($ekey);
						$odu = User::where('openid',$message['FromUserName'])->first();
						if(!empty($odu))return "绑定失败！\n您已经绑定过，请联系管理员解绑后重新绑定。";
						$us = User::find($wb->uid);
						if(empty($us))return "绑定失败！\n找不到该用户，请联系管理员。";
						if($us['openid'] == $message['FromUserName'])return "您已经绑定过，无需重复绑定。";
						if(!empty($us['openid']))return "绑定失败！\n该用户已经绑定过，请联系管理员解绑后重新绑定。";
						$us->openid = $message['FromUserName'];
						$us->save();
						return "绑定成功了！\n您已经成功绑定，可直接使用微信登录本平台。";
						break;
					case 'lookquota':
						$stat = Order::where('id',$wb->uid)->value('status');
						if($stat>=2)return "本订单已经确认，无须再次扫码";
						//$link = 'http://yp-dev.one2fit.cn/quotation?val='.encrypt($wb->uid);
						$link = url('/quotation').'?val='.encrypt($wb->uid);
						return "<a href='".$link."'>查看报价单</a>";
						break;
				}
				
			}
		});
		$response = $app->server->serve();
		return $response;
	}
	//微信支付回调
	public function WechatPay(Request $request){
		$app = Factory::payment(config('wechatpay'));
		$response = $app->handlePaidNotify(function($message, $fail){
    // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
	Log::debug($message);
    $order = Payorder::where('trade_no',$message['out_trade_no'])->first();

    if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
        return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
    }

    ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

    if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
        // 用户是否支付成功
        if (array_get($message, 'result_code') === 'SUCCESS') {
			$newday = now();
            $order->paid_at = now(); // 更新支付时间为当前时间
            $order->status = 'paid';
			
			$flight = Tenant::find($order->tenat_id);
			$flight->tenant_level_id = $order->product_id;
			
			$exday = Carbon::parse($flight->expired_at);
			if($exday >= $newday)$newday = $exday;
			switch($order->pay_type){
				case 'quarter_price':
					$newday=$newday->addMonths(3);break;
				case 'year_price':
					$newday=$newday->addYears(1);break;
			}
			$flight->expired_at = $newday;
        // 用户支付失败
        } elseif (array_get($message, 'result_code') === 'FAIL') {
            $order->status = 'paid_fail';
        }
    } else {
        return $fail('通信失败，请稍后再通知我');
    }
	$flight->save();
    $order->save(); // 保存订单

    return true; // 返回处理完成
});
		return $response;
	}
	public function Vercode(Request $request){
		header ('Content-Type: image/png');
		$image=imagecreatetruecolor(100, 30);
		$color=imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 20, 20, $color);
		$code='';
		for($i=0;$i<4;$i++){
			$fontSize=8;
			$x=rand(5,10)+$i*100/4;
			$y=rand(5, 15);
			$data='abcdefghijklmnopqrstuvwxyz123456789';
			$string=substr($data,rand(0, strlen($data)),1);
			$code.=$string;
			$color=imagecolorallocate($image,rand(0,120), rand(0,120), rand(0,120));
			imagestring($image, $fontSize, $x, $y, $string, $color);
		}
		session(['code' => $code]);
		for($i=0;$i<200;$i++){
			$pointColor=imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
			imagesetpixel($image, rand(0, 100), rand(0, 30), $pointColor);
		}
		for($i=0;$i<2;$i++){
			$linePoint=imagecolorallocate($image, rand(150, 255), rand(150, 255), rand(150, 255));
			imageline($image, rand(10, 50), rand(10, 20), rand(80,90), rand(15, 25), $linePoint);
		}
		imagepng($image);
		imagedestroy($image);
	}
}
