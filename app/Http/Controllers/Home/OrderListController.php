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

class OrderListController extends Controller
{
	function WechatOauth(){
		// 未登录
		$app = Factory::officialAccount(config('wechat'));
		$oauth = $app->oauth;
		session(['jssdk' => $app->jssdk->buildConfig(array('closeWindow'), false,false,false)]);
		//$cookie = cookie('target_url', 'value', $minutes);
		session(['target_url' => '/customer/orderlist']);
		return $oauth->redirect();
	}
    public function GetOrders(Request $request){
		$search = array();
			$request->get('orde') && $search['orde'] = $request->get('orde');
			$request->get('stat') && $search['stat'] = $request->get('stat');
			$request->get('teid') && $search['teid'] = $request->get('teid');
			
			if($request->get('stat') !== null){
				
			}
		$app = Factory::officialAccount(config('wechat'));
		$oauth = $app->oauth;
		if(empty(session('wechat_user'))){
			session(['target_url' => url()->full()]);
			return $oauth->redirect();
		}
		$user = session('wechat_user');
		$data = array();
		$data['jssdk'] = $app->jssdk->buildConfig(array('closeWindow'), false,false,false);

		$cusid = Customer::where('openid',$user['id'])->pluck('id');
		//dd($cusid);
		if($cusid->count() == 0)abort(403, '您还不是客户，不能使用此功能。');
		//dd(array_wrap($cusid));
		$orders = Order::whereIn('orders.customer_id',$cusid)
							->where(function($query) use (&$search){
								foreach($search as $key => $val){
									switch($key){
										case 'orde':
											$query->where('orders.no', 'like', '%'.$val.'%');break;
										case 'teid':
											$query->where('tenants.id', '=', $val);break;
										case 'stat':
											switch($val){
												case '所有订单':
													break;
												case '等待审核':
													$query->where('orders.status', '=' , 0);break;
												case '确认报价单':
													$query->where('orders.status', '=' , 1);break;
												case '准备生产':
													$query->where('orders.status', '=' , 2);break;
												case '生产中':
													$query->where('orders.status', '=' , 3);break;
												case '生产完成':
													$query->where('orders.status', '=' , 4);break;
												case '已发货':
													$query->where('orders.status', '=' , 5);break;
												case '完成':
													$query->where('orders.status', '=' , 6);break;
											}
											break;
									}
								}
							})
							->leftJoin('quotations', 'orders.id', '=', 'quotations.order_id')
							->leftJoin('customers', 'customers.id', '=', 'orders.customer_id')
							->leftJoin('tenants', 'tenants.id', '=', 'orders.tenat_id')
							->select('orders.id','customers.name as cusname','tenants.name as tenname','orders.no','orders.status','orders.parts','quotations.total','orders.contract','orders.created_at')
							->orderBy('id', 'desc')
							->paginate(5);
		$orders->appends($search)->links();
		
		$out = $orders->toArray();
		
		foreach($out['data'] as $key => $val){
			$parts = Part::whereIn('parts.id',json_decode($val['parts'],true))
				->leftJoin('materials', 'materials.id', '=', 'parts.material_id')
				->leftJoin('molding_processes', 'molding_processes.id', '=', 'parts.molding_process_id')
				->leftJoin('surfaces', 'surfaces.id', '=', 'parts.surface_id')
				->leftJoin('equipments', 'equipments.id', '=', 'parts.equipment_id')
				->select('parts.id','parts.name','parts.product_num','parts.diagram','materials.name as matname', 'molding_processes.name as modname', 'surfaces.name as surname', 'equipments.mname as equname','parts.status')
				->orderBy('parts.id', 'desc')
				->get();
			$valt = encrypt($val['id']);
			$out['data'][$key]['parts'] = $parts->toArray();
			$out['data'][$key]['quoturl'] = env('APP_URL').'/quotation?val='.$valt;//报价单
			$out['data'][$key]['proplan'] = env('APP_URL').'/wechat/order/procuplan?val='.$valt;//生产计划
			$out['data'][$key]['logistics'] = env('APP_URL').'/wechat/order/logistics?val='.$valt;//送货单
			$out['data'][$key]['contract'] = env('APP_URL').'/wechat/order/contract?val='.$valt;//合同
			$out['data'][$key]['evaluate'] = env('APP_URL').'/wechat/order/evaluate?val='.$valt;//评价
			$out['data'][$key]['aftersale'] = env('APP_URL').'/wechat/order/aftersale?val='.$valt;//售后
			$out['data'][$key]['reload'] = env('APP_URL').'/wechat/order/reload?val='.$valt;//再次下单
		}
		$tenid = Customer::where('openid',$user['id'])->pluck('tenat_id');
		$tenlist = Tenant::whereIn('id',$tenid)->select('id','name')->get();
		$data['val'] = $out;
		if(!empty($search['teid'])){
			$data['tenanlist']['text'] = Tenant::where('id',$search['teid'])->value('name');
		}
		$data['search'] = $search;
		
		$data['tenanlist']['values'] = $tenlist->pluck('id');
		$data['tenanlist']['displayValues'] = $tenlist->pluck('name');
		//dd($data);
		return view('wechat.orderlist')->with('data', $data);
	}
	public function MessageList(Request $request){
		return view('wechat.customer.message');
	}
	public function Setting(Request $request){
		$app = Factory::officialAccount(config('wechat'));
		$oauth = $app->oauth;
		if(empty(session('wechat_user'))){
			session(['target_url' => url()->full()]);
			return $oauth->redirect();
		}
		$user = session('wechat_user');
		$cust = Customer::where('customers.openid',$user['id'])
							->leftJoin('tenants', 'tenants.id', '=', 'customers.tenat_id')
							->leftJoin('customer_types', 'customer_types.id', '=', 'customers.customer_type_id')
							->select('customers.*','tenants.name as tename','customer_types.name as ctname')
							->first();
		$lkmans = Linkman::where('customer_id',$cust->id)->get();
		
		$tenid = Customer::where('openid',$user['id'])->pluck('tenat_id');
		$tans = Tenant::whereIn('id',$tenid)->select('id','name')->get();
		
		$data['user'] = $user;
		$data['cust'] = $cust->toArray();
		$data['tans'] = $tans->toArray();
		$data['cust']['lkmans'] = $lkmans->toArray();
		$data['cust']['ticket_info'] = unserialize($cust->ticket_info);
		return view('wechat.customer.setting')->with('data', $data);
	}
	public function Hepl(Request $request){
		return view('wechat.customer.help');
	}
}
