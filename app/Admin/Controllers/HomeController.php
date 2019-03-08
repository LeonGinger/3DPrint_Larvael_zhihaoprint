<?php

namespace App\Admin\Controllers;

use Excel;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Dingo\Api\Http\Request;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('图表数据')
            ->row(new Box('数据总览', view('admin.home.panel-group')))
            ->body(new Box('商户数据', view('admin.home.chart')));
    }

    public function tenantLists(Content $content)
    {
        $type = \request()->get('type');
        $rules = [
            'type' => 'required|in:all,new,expired,pre_expired',
            'tenant_type' => 'exists:tenant_types,id|nullable',
            'action' => 'in:export|nullable',
        ];
        $messages = [
            'type.required' => '类型不能为空',
            'type.in' => '类型错误',
        ];
        $validator = \Validator::make(\request()->all(), $rules, $messages);
        if ($validator->fails()) {
            admin_error('提示', $validator->errors()->first());

            return redirect()->back();
        }

        $tenant_type = \request()->get('tenant_type');
        $action = \request()->get('action');

        $tenant_types = TenantType::all();

        $query = Tenant::query()->with('tenantLevel', 'tenantType')
            ->withCount(['orders', 'customers']);

        $today = Carbon::now()->format('Y-m-d');
        $start_date = \request()->get('start_date') ? \request()->get('start_date') : Carbon::now()->startOfMonth()->toDateString();
        $end_date = \request()->get('end_date') ? \request()->get('end_date') : Carbon::now()->endOfMonth()->toDateString();

        switch ($type) {
            case 'all':
                $title = '所有商家';
                break;
            case 'new':
                $query->whereBetween(\DB::raw('DATE(created_at)'), [$start_date, $end_date]);
                $title = '新入驻商家';
                break;
            case 'expired':
                $title = '已过期商家';
                $query->whereDate('expired_at', '<', $today);
                break;
            case 'pre_expired':
                $title = '即将过期商家';
                $query->whereBetween(\DB::raw('DATE(expired_at)'), [$start_date, $end_date]);
                break;
        }
        if ($tenant_type) {
            $query->where('tenant_type_id', $tenant_type);
        }

        if ($action == 'export') {
            $results = $query->get();
            $cell_data = [
                ['名称', '联系人', '联系电话', '商家等级', '商家行业', '省', '市', '区', '详细地址', '状态'],
            ];
            foreach ($results as $result) {
                if (!isset($result->name)) {
                    Tenant::where('id', $result->id)->delete();
                } else {
                    $cell_data[] = [
                        $result->name,
                        $result->linkman,
                        $result->phone,
                        $result->tenantLevel->name,
                        $result->tenantType->name,
                        $result->province,
                        $result->city,
                        $result->area,
                        $result->address,
                        $result->status ? '启用' : '禁用',
                    ];
                }
            }
            $filename = $title . date('Ymd');
            Excel::create($filename, function ($excel) use ($cell_data) {
                $excel->sheet('tenants', function ($sheet) use ($cell_data) {
                    $sheet->rows($cell_data);
                });
            })->export('xls');
        }

        $all_count = Tenant::count();
        $new_count = Tenant::whereBetween(\DB::raw('DATE(created_at)'), [$start_date, $end_date])->count();
        $expired_count = Tenant::whereDate('expired_at', '<', $today)->count();
        $pre_expired_count = Tenant::whereBetween(\DB::raw('DATE(expired_at)'), [$start_date, $end_date])->count();

        $tenants = $query->paginate(8);
        $summary = collect();
        $summary->put('all_count', $all_count);
        $summary->put('new_count', $new_count);
        $summary->put('expired_count', $expired_count);
        $summary->put('pre_expired_count', $pre_expired_count);

        $tenants_with_count = Tenant::withCount(['orders', 'customers'])->get();
        $tenant_orders_count = 0;
        $tenant_customers_count = 0;
        foreach ($tenants_with_count as $item) {
            $tenant_orders_count += $item->orders_count;
            $tenant_customers_count += $item->customers_count;
        }
//        dd($tenants_with_count->toArray());
        $counts['orders_count'] = $tenant_orders_count;
        $counts['customers_count'] = $tenant_customers_count;
//        dd($tenants->toArray());
        return $content->header('统计报表')
            ->description(' ')
            ->body(new Box($title, view('admin.tenants.lists', compact('tenants', 'summary', 'tenant_types', 'counts'))));
    }

    public function exportTenantOrders()
    {
        $tenant_id = \request()->get('tenant_id');
        $tenant = Tenant::findOrFail($tenant_id);
        $orders = $tenant->orders()->with(['customer'])->get();
        $cell_data = [
            ['编号', '商家名称', '客户名称',  '联系人列表', '零件寄送地址', '发票寄送地址', '项目经理', '项目工程师', '销售工程师', '零件列表', '税费', '运费', '合同路径'],
        ];
        foreach ($orders as $result) {
            $cell_data[] = [
                $result->no,
                $tenant->name,
                $result->customer->name,
                $result->lkmans,
                $result->postaddr,
                $result->invoaddr,
                $result->manger,
                $result->pojer,
                $result->saler,
                $result->parts,
                $result->taxation,
                $result->freight,
                $result->contract,
            ];
        }
        $filename = "商家[{$tenant->name}]订单" . date('Ymd');
        Excel::create($filename, function ($excel) use ($cell_data) {
            $excel->sheet('tenant_order', function ($sheet) use ($cell_data) {
                $sheet->rows($cell_data);
            });
        })->export('xls');
    }

    public function tenantData(Request $request)
    {
        $start_date = \request()->get('start_date');
        $end_date = \request()->get('end_date');

        $period = CarbonPeriod::create($start_date, $end_date);
        $results = [];

        foreach ($period as $key =>  $date) {
            $formated_date = $date->format('Y-m-d');
            $count = Tenant::whereDate('created_at', $formated_date)
                ->count();
            $results[$key]['name'] = $date->format('n/d');
            $results[$key]['value'] = $count;
        }
        $response = [
            'code' => 0,
            'message' => 'success',
            'data' => $results,
        ];
        echo json_encode($results);
        exit;
    }
}
