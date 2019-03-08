<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Part;
use App\Models\User;
use App\Models\Delivery_note;
use App\Models\Delivery;

class LogisticsController extends Controller
{
    public function Logistics(Request $request){
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
		
		$oid = decrypt($request->Input('val'));
		$order = Order::find($oid);
		if(empty($order))return response('无订单信息！', 200);
		
		$dn = Delivery_note::where('delivery_notes.orderno', $order->no)
		//$dn = Delivery_note::where('delivery_notes.orderno', '201811230459455557')
						->select('id','no','distribution_mode','parts','status', 'express_name', 'express_code', 'express_no', 'disk', 'created_at')
						->orderBy('id', 'desc')
						->get();
		$out = $dn->toArray();
		foreach($out as $key=>$val){
			$dddid = $val['id'];
			$par = Part::whereIn('parts.id',json_decode($val['parts'],true))
						 ->join('deliverys', function ($join) use( $dddid ) {
							$join->on('deliverys.part_id', '=', 'parts.id')
							->where('deliverys.delivery_note_id', '=', $dddid);
						})
						->select('parts.name','parts.diagram','parts.product_num','deliverys.yscount','parts.status')
						->get();
			$out[$key]['parts'] = $par->toArray();
			$out[$key]['confirm'] = encrypt($val['id']);
		}
		$data['val'] = $out;
		$data['no'] = $order->no;
		return view('wechat.logistics')->with('data', $data);
	}
}
