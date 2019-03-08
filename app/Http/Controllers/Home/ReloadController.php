<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use EasyWeChat\Factory;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Part;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\User;
use App\Models\Linkman;
use App\Models\Evaluate;
use App\Models\ReturnNote;

use Illuminate\Contracts\Encryption\DecryptException;

class ReloadController extends Controller
{
    public function Index(Request $request){
		$app = Factory::officialAccount(config('wechat'));
		$oauth = $app->oauth;
		// 未登录
		if (empty(session('wechat_user'))) {
			session(['target_url' => url()->full()]);
			return $oauth->redirect();
		}
		$user = session('wechat_user');
		
		$data = array();
		$data['jssdk'] = $app->jssdk->buildConfig(array('closeWindow'), false,false,false);
		
		$oid = urldecode($request->Input('val'));
		$data['oid'] = $oid;
		
		try {
			$oid = decrypt($oid);
		} catch (DecryptException $e) {
			abort(403, '无订单信息');
		}
		
		$order = Order::find($oid);
		if(empty($order))abort(403, '无订单信息');
		$quot = Quotation::where('order_id', $oid)->first();
		if(empty($quot))abort(403, '报价单错误');
		$out = $quot->toArray();
		
		$parts = Part::whereIn('parts.id', json_decode($order->parts,true))
						->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
						->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
						->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
						->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
						->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname')
						->get();
		
		$out['parts'] = $parts->toArray();
		$out['no'] = $order->no;
		$out['crdate'] = $order->created_at;
		$out['status'] = $order->status;
		
		$data['val'] = $out;
		
		return view('wechat.reload')->with('data', $data);
		//return view('wechat.evaluate');
	}
}
