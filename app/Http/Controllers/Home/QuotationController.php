<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Part;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Linkman;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Contracts\Encryption\DecryptException;

class QuotationController extends Controller
{
    public function GetQuotation(Request $request){
		$app = Factory::officialAccount(config('wechat'));
		$oauth = $app->oauth;
		// 未登录
		if (empty(session('wechat_user'))) {
			//$cookie = cookie('target_url', 'value', $minutes);
			session(['target_url' => url()->full()]);
			return $oauth->redirect();
		}
		$user = session('wechat_user');
		
		$data = array();
		$data['jssdk'] = $app->jssdk->buildConfig(array('closeWindow'), false,false,false);
		
		$oid = urldecode($request->Input('val'));
		
		try {
			$oid = decrypt($oid);
		} catch (DecryptException $e) {
			abort(403, '无订单信息');
		}
		
		$order = Order::find($oid);
		if(empty($order))abort(403, '无订单信息');
		$quot = Quotation::where('order_id', $oid)->first();
		if(empty($quot))abort(403, '报价单错误');
		$cus = Customer::where('id', $order->customer_id)->select('name','ticket_info','openid')->first();
		if(empty($cus->openid)){
			Customer::where('id', $order->customer_id)->update(['openid' => $user['id']]);
		}
		$lks = explode(',', $order->lkmans);
		$lks = Linkman::whereIn('id', $lks)->select('linkman_name','lk_phone')->get();
		
		$parts = Part::whereIn('parts.id', json_decode($order->parts,true))
						->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
						->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
						->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
						->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
						->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname')
						->selectRaw('parts.coefficient * parts.price as couprice')
						->get();
		
		$out = $quot->toArray();
		$out['cusname'] = $cus->name;
		$out['tanname'] = Tenant::where('id',$order->tenat_id)->value('name');
		$out['ticket_info'] = unserialize($cus->ticket_info);
		$out['linkmans'] = $lks->toArray();
		$out['manger'] = User::where('id', $order->manger)->select('name','phone')->first();
		$out['pojer'] = User::where('id', $order->pojer)->select('name','phone')->first();
		$out['saler'] = User::where('id', $order->saler)->select('name','phone')->first();
		$out['parts'] = $parts->toArray();
		$out['taxation'] = $order->taxation;
		$out['freight'] = $order->freight;
		$out['postaddr'] = Linkman::where('id', $order->postaddr)->select('linkman_name','lk_phone','province','city','area','lk_address')->first();
		$out['invoaddr'] = Linkman::where('id', $order->invoaddr)->select('linkman_name','lk_phone','province','city','area','lk_address')->first();
		$out['status'] = $order->status;
		$out['val'] = urldecode($request->Input('val'));
		$data['val'] = $out;
		return view('wechat.quotation')->with('data', $data);
	}
}
