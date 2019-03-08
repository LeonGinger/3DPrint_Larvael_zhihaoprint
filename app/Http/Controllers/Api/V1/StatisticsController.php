<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Part;
use App\Models\Molding;
use App\Models\Materials;
use App\Models\Surfaces;
use App\Models\Equipment;
use App\Models\ReturnNote;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    //所有订单数量
	public function AllOrder(Request $request)
    {
		$type = empty($request->type) ? 'count' : $request->type;
		switch($type){
			case 'count':
				$count = Order::where('tenat_id', $this->GetLoginUser()->tenant_id)->count();
				return $this->response->array(['num'=>$count]);
				break;
			case 'todaycount':
				$count = Order::where('tenat_id', $this->GetLoginUser()->tenant_id)->whereRaw('date(created_at) = date(now())')->count();
				return $this->response->array(['num'=>$count]);
				break;
			case 'ordstst':
				$count = Order::where('tenat_id', $this->GetLoginUser()->tenant_id)
							->selectRaw('COUNT(*) as total')
							->selectRaw('SUM(IF(status = 0, 1, 0)) as status0')
							->selectRaw('SUM(IF(status = 1, 1, 0)) as status1')
							->selectRaw('SUM(IF(status = 2, 1, 0)) as status2')
							->selectRaw('SUM(IF(status = 3, 1, 0)) as status3')
							->selectRaw('SUM(IF(status = 4, 1, 0)) as status4')
							->selectRaw('SUM(IF(status = 5, 1, 0)) as status5')
							->selectRaw('SUM(IF(status = 6, 1, 0)) as status6')
							->first();
				return $this->response->array($count->toArray());
				break;
			case 'ordpart':
				$count = Order::where('tenat_id', $this->GetLoginUser()->tenant_id)
							->leftJoin('parts', 'parts.order_id', '=', 'orders.id')
							->select('orders.no as orderno')
							->selectRaw('COUNT(parts.id) as total')
							->groupBy('orders.id')
							->get();
				return $this->response->array($count->toArray());
				break;
		}
	}
	//所有客户数量
	public function AllCustomer(Request $request)
    {
		$type = empty($request->type) ? 'count' : $request->type;
		switch($type){
			case 'count':
				$count = Customer::where('tenat_id', $this->GetLoginUser()->tenant_id)->count();
				return $this->response->array(['num'=>$count]);
				break;
			case 'todaycount':
				$count = Customer::where('tenat_id', $this->GetLoginUser()->tenant_id)->whereRaw('date(created_at) = date(now())')->count();
				return $this->response->array(['num'=>$count]);
				break;
			case 'cusords':
				$count = Customer::where('customers.tenat_id', $this->GetLoginUser()->tenant_id)
									->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
									->select('customers.name as cusnum')
									->selectRaw('COUNT(orders.no) as total')
									->groupBy('customers.id')
									->get();
				return $this->response->array($count->toArray());
				break;
			case 'cuspart':
				$count = Customer::where('customers.tenat_id', $this->GetLoginUser()->tenant_id)
									->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
									->leftJoin('parts', 'parts.order_id', '=', 'orders.id')
									->select('customers.name as cusnum')
									->selectRaw('COUNT(parts.id) as total')
									->groupBy('customers.id')
									->get();
				return $this->response->array($count->toArray());
				break;
		}
		
	}
	//所有订单数量
	public function AllPart(Request $request)
    {
		$type = empty($request->type) ? 'count' : $request->type;
		switch($type){
			case 'count':
				$count = Customer::where('customers.tenat_id', $this->GetLoginUser()->tenant_id)
									->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
									->leftJoin('parts', 'parts.order_id', '=', 'orders.id')
									->count();
				return $this->response->array(['num'=>$count]);
				break;
			case 'todaycount':
				$count = Customer::where('customers.tenat_id', $this->GetLoginUser()->tenant_id)
									->whereRaw('date(parts.created_at) = date(now())')
									->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
									->leftJoin('parts', 'parts.order_id', '=', 'orders.id')
									->count();
				return $this->response->array(['num'=>$count]);
				break;
			case 'ordstst':
				$count = Order::where('tenat_id', $this->GetLoginUser()->tenant_id)
							->selectRaw('COUNT(*) as total')
							->selectRaw('SUM(IF(status = 0, 1, 0)) as status0')
							->selectRaw('SUM(IF(status = 1, 1, 0)) as status1')
							->selectRaw('SUM(IF(status = 2, 1, 0)) as status2')
							->selectRaw('SUM(IF(status = 3, 1, 0)) as status3')
							->selectRaw('SUM(IF(status = 4, 1, 0)) as status4')
							->selectRaw('SUM(IF(status = 5, 1, 0)) as status5')
							->selectRaw('SUM(IF(status = 6, 1, 0)) as status6')
							->first();
				return $this->response->array($count->toArray());
				break;
			case 'ordpart':
				$count = Order::where('tenat_id', $this->GetLoginUser()->tenant_id)
							->leftJoin('parts', 'parts.order_id', '=', 'orders.id')
							->select('orders.no as orderno')
							->selectRaw('COUNT(parts.id) as total')
							->groupBy('orders.id')
							->get();
				return $this->response->array($count->toArray());
				break;
		}
	}
	//所有订单数量
	public function AllInfo(Request $request)
    {
		$type = empty($request->type) ? 'count' : $request->type;
		$tid = $this->GetLoginUser()->tenant_id;
		switch($type){
			case 'molding':
				$count = Molding::where('molding_processes.tenat_id',$tid)
							->orWhere('molding_processes.tenat_id',0)
							->leftJoin('parts', function ($join)use($tid) {
								$join->on('parts.molding_process_id', '=', 'molding_processes.id')
									 ->whereRaw('parts.order_id IN(select orders.id from orders where orders.tenat_id = ?)', [$tid]);
							})
 
							->select('molding_processes.name')
							->selectRaw('COUNT(parts.id) as total')
							->groupBy('molding_processes.id')
							->get();
				return $this->response->array($count->toArray());
				break;
			case 'materials':
				$count = Materials::where('materials.tenat_id',$tid)
							->orWhere('materials.tenat_id',0)
							->leftJoin('parts', function ($join)use($tid) {
								$join->on('parts.material_id', '=', 'materials.id')
									 ->whereRaw('parts.order_id IN(select orders.id from orders where orders.tenat_id = ?)', [$tid]);
							})
 
							->select('materials.name')
							->selectRaw('COUNT(parts.id) as total')
							->groupBy('materials.id')
							->get();
				return $this->response->array($count->toArray());
				break;
			case 'surfaces':
				$count = Surfaces::where('surfaces.tenat_id',$tid)
							->orWhere('surfaces.tenat_id',0)
							->leftJoin('parts', function ($join)use($tid) {
								$join->on('parts.surface_id', '=', 'surfaces.id')
									 ->whereRaw('parts.order_id IN(select orders.id from orders where orders.tenat_id = ?)', [$tid]);
							})
 
							->select('surfaces.name')
							->selectRaw('COUNT(parts.id) as total')
							->groupBy('surfaces.id')
							->get();
				return $this->response->array($count->toArray());
				break;
			case 'equipment':
				$count = Equipment::where('equipments.tenat_id',$tid)
							->orWhere('equipments.tenat_id',0)
							->leftJoin('parts', function ($join)use($tid) {
								$join->on('parts.equipment_id', '=', 'equipments.id')
									 ->whereRaw('parts.order_id IN(select orders.id from orders where orders.tenat_id = ?)', [$tid]);
							})
 
							->select('equipments.mname as name')
							->selectRaw('COUNT(parts.id) as total')
							->groupBy('equipments.id')
							->get();
				return $this->response->array($count->toArray());
				break;
		}
	}
	//所有订单数量
	public function AllReturn(Request $request)
    {
		$count = ReturnNote::where('tenat_id', $this->GetLoginUser()->tenant_id)
							->selectRaw('COUNT(*) as total')
							->selectRaw('SUM(IF(returned_type = 0, 1, 0)) as type0')
							->selectRaw('SUM(IF(returned_type = 1, 1, 0)) as type1')
							->first();
				return $this->response->array($count->toArray());
	}
	//7日内订单数量
	public function ByDay(Request $request)
    {
		$type = empty($request->type) ? 'order' : $request->type;
		$tid = $this->GetLoginUser()->tenant_id;
		$sql = 'select a.click_date, ifnull(b.count, 0) as count from (
					SELECT curdate() as click_date 
					union all 
					SELECT date_sub(curdate(),interval 1 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 2 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 3 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 4 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 5 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 6 day) as click_date 
					) a left join (select date(created_at) as datetime, count(*) as count from tablename WHERE tenat_id = ? group by date(created_at)) b on a.click_date = b.datetime';
		switch($type){
			case 'order':
				$count = DB::select(str_replace('tablename', 'orders',$sql),[$tid]);
				return $this->response->array($count);
				break;
			case 'customer':
				$count = DB::select(str_replace('tablename', 'customers',$sql),[$tid]);
				return $this->response->array($count);
				break;
		}
	}
	//24小时内订单数量
	public function ByHour(Request $request)
    {
		$type = empty($request->type) ? 'order' : $request->type;
		$tid = $this->GetLoginUser()->tenant_id;
		
		$sql = "select a.click_date,ifnull(b.count,0) as count
						from (
						   SELECT 0  as click_date
							union all
							SELECT 1 as click_date
							union all
							SELECT 2 as click_date
							union all
							SELECT 3 as click_date
							union all
							SELECT 4 as click_date
							union all
							SELECT 5 as click_date
							union all
							SELECT 6 as click_date
								union all
							SELECT 7 as click_date
								union all
							SELECT 8 as click_date
								union all
							SELECT 9 as click_date
								union all
							SELECT 10 as click_date
								union all
							SELECT 11 as click_date
								union all
							SELECT 12 as click_date
								union all
							SELECT 13 as click_date
								union all
							SELECT 14 as click_date
								union all
							SELECT 15 as click_date
								union all
							SELECT 16 as click_date
								union all
							SELECT 17 as click_date
								union all
							SELECT 18 as click_date
								union all
							SELECT 19 as click_date
								union all
							SELECT 20 as click_date
								union all
							SELECT 21 as click_date
								union all
							SELECT 22 as click_date
								union all
							SELECT 23 as click_date
						) a left join (
							SELECT
								HOUR (created_at) AS hours,
								count(*) AS count
								FROM
									tablename
								WHERE
									date(created_at) = date(now()) and tenat_id = ?
								GROUP BY
								hours
						) b on a.click_date = b.hours";
		
		switch($type){
			case 'order':
				$count = DB::select(str_replace('tablename', 'orders',$sql),[$tid]);
				return $this->response->array($count);
				break;
			case 'customer':
				$count = DB::select(str_replace('tablename', 'customers',$sql),[$tid]);
				return $this->response->array($count);
				break;
		}
	}
	//1个月内订单数量
	public function ByMoon(Request $request)
    {
		$type = empty($request->type) ? 'order' : $request->type;
		$tid = $this->GetLoginUser()->tenant_id;
		$sql = 'select a.click_date, ifnull(b.count, 0) as count from (
					SELECT curdate() as click_date 
					union all 
					SELECT date_sub(curdate(),interval 1 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 2 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 3 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 4 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 5 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 6 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 7 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 8 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 9 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 10 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 11 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 12 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 13 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 14 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 15 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 16 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 17 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 18 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 19 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 20 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 21 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 22 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 23 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 24 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 25 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 26 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 27 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 28 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 29 day) as click_date 
					union all 
					SELECT date_sub(curdate(),interval 30 day) as click_date 
					) a left join (select date(created_at) as datetime, count(*) as count from tablename WHERE tenat_id = ? group by date(created_at)) b on a.click_date = b.datetime';
		switch($type){
			case 'order':
				$count = DB::select(str_replace('tablename', 'orders',$sql),[$tid]);
				return $this->response->array($count);
				break;
			case 'customer':
				$count = DB::select(str_replace('tablename', 'customers',$sql),[$tid]);
				return $this->response->array($count);
				break;
		}
	}
}
