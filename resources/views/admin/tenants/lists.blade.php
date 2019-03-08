
<div class="row center-block">
    <div class="col-md-3 col-sm-12">
        <div class="list-group">
            <a href="{{ route('admin.tenant.list', ['type' => 'all', 'tenant_type' => request()->get('tenant_type'), 'start_date' => '', 'end_date' => '']) }}" class="list-group-item {{ request()->get('type') == 'all' ? 'active': '' }}">所有商家 <span class="badge">{{ $summary['all_count'] }}</span></a>
            <a href="{{ route('admin.tenant.list', ['type' => 'new', 'tenant_type' => request()->get('tenant_type'), 'start_date' => \Carbon\Carbon::now()->startOfMonth()->toDateString(), 'end_date' => \Carbon\Carbon::now()->endOfMonth()->toDateString()]) }}" class="list-group-item {{ request()->get('type') == 'new' ? 'active': '' }}">新入驻商家 <span class="badge">{{ $summary['new_count'] }}</span></a>
            <a href="{{ route('admin.tenant.list', ['type' => 'pre_expired', 'tenant_type' => request()->get('tenant_type'), 'start_date' => \Carbon\Carbon::now()->startOfMonth()->toDateString(), 'end_date' => \Carbon\Carbon::now()->endOfMonth()->toDateString()]) }}" class="list-group-item {{ request()->get('type') == 'pre_expired' ? 'active': '' }}">即将过期商家 <span class="badge">{{ $summary['pre_expired_count'] }}</span></a>
            <a href="{{ route('admin.tenant.list', ['type' => 'expired', 'tenant_type' => request()->get('tenant_type'), 'start_date' => \Carbon\Carbon::now()->startOfMonth()->toDateString(), 'end_date' => \Carbon\Carbon::now()->endOfMonth()->toDateString()]) }}" class="list-group-item {{ request()->get('type') == 'expired' ? 'active': '' }}">已过期商家 <span class="badge">{{ $summary   ['expired_count'] }}</span></a>
        </div>
    </div>
    <div class="col-md-9 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <form class="form-inline" method="get" action="{{ route('admin.tenant.list') }}">
                    <div class="form-group">
                        <label for="type" class="sr-only">行业</label>
                        <select class="form-control" name="tenant_type" id="type">
                            <option value="">== 请选择行业 ==</option>
                            @foreach($tenant_types as $tenant_type)
                                <option {{ request()->get('tenant_type') && request()->get('tenant_type') == $tenant_type->id ? 'selected' : '' }} value="{{ $tenant_type->id }}">{{ $tenant_type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="type" value="{{ request()->get('type') }}">
                    <div class="form-group" style="margin-left: 10px;">
                        <label for="type" class="sr-only">开始时间</label>
                        <input type="text" id="start_date" name="start_date" placeholder="开始时间" class="form-control">
                    </div>
                    <span> - </span>
                    <div class="form-group">
                        <label for="type" class="sr-only">结束时间</label>
                        <input type="text" id="end_date" name="end_date" placeholder="结束时间" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;">查询</button>
                    <a href="{{ route('admin.tenant.list', ['type' => request()->get('type')]) }}" class="btn btn-info" style="margin: 0 10px;">重置</a>
                    <a href="{{ route('admin.tenant.list', ['type' => \request()->get('type'), 'tenant_type' => \request()->get('tenant_type'), 'action' => 'export']) }}" target="_blank" class="btn btn-success" download>导出所有商家（Excel）</a>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" style="overflow: hidden;">
                <h3 class="panel-title pull-left">所有商家</h3>
                <div class="pull-right">
                    <span>客户总数：{{ $counts['customers_count'] }}</span>
                    <span style="margin: 0 20px;">订单总数：{{ $counts['orders_count'] }}</span>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered text-nowrap">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>商家名称</th>
                            <th>联系人</th>
                            <th>手机号码</th>
                            <th>商家等级</th>
                            <th>商家行业</th>
                            <th>限制客户</th>
                            <th>限制订单</th>
                            <th>客户数</th>
                            <th>订单数</th>
                            <th>过期时间</th>
                            @if(request()->get('type') == 'expired')
                                <th>过期天数</th>
                            @endif
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($tenants)
                            @foreach($tenants as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->linkman }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{{ $item->tenantLevel->name }}</td>
                                    <td>{{ $item->tenantType->name }}</td>
                                    <td>{{ $item->tenantLevel->customer_numbers }}</td>
                                    <td>{{ $item->tenantLevel->order_numbers }}</td>
                                    <td>{{ $item->customers_count }}</td>
                                    <td>{{ $item->orders_count }}</td>
                                    <td>{{ $item->expired_at }}</td>
                                    @if(request()->get('type') == 'expired')
                                        <td>{{  \Carbon\Carbon::parse($item->expired_at)->diffInDays(now()) }}</td>
                                    @endif
                                    <td>
                                        @if($item->orders_count > 0)
                                            <a href="{{ route('admin.tenant.orders.export', ['tenant_id' => $item->id]) }}" target="_blank" download>下载订单</a>
                                        @else
                                            <a href="javascript:void(0);" onclick="alert('该商家目前没有订单')">下载订单</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td rowspan="7">暂无相关数据</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div>{{ $tenants->links() }}</div>
            </div>
        </div>
    </div>
</div>
<script>
    laydate.render({
        elem: '#start_date'
        ,value: '{{ request()->get('start_date') }}'
    });
    laydate.render({
        elem: '#end_date'
        ,value: '{{ request()->get('end_date') }}'
    });
</script>
