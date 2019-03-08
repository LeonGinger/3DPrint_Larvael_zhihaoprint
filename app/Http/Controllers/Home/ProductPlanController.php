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
use App\Models\Product_plan;

use Illuminate\Contracts\Encryption\DecryptException;

class ProductPlanController extends Controller
{
    public function PartPlanList(Request $request){
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
		if(empty($order))return response('无订单信息！', 200);
		
		$parts = Part::whereIn('parts.id', json_decode($order->parts,true))
						->orWhere('order_id',$oid)
						->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
						->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
						->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
						->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
						->select('parts.*','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname')
						->orderBy('parts.id', 'desc')
						->get();
		$out = $parts->toArray();
		foreach($out as $key=>$val){
			$prlan = Product_plan::where('part_id',$val['id'])->first();
			$out[$key]['plan'] = empty($prlan) ? null : $prlan->toArray();
		}
		$data['val'] = $out;
		$data['no'] = $order->no;
		return view('wechat.productplan')->with('data', $data);
	}
}
