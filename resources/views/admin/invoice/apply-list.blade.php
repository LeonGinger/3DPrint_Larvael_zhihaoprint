<div class="panel panel-default">
    <div class="panel-body">
        <form class="form-inline" method="get" action="{{ route('admin.invoice.apply_list') }}">
            <div class="form-group">
                <label class="sr-only" for="rise">名称</label>
                <input type="text" class="form-control" name="rise" value="{{ request()->get('rise') }}" id="rise" placeholder="输入单位名称">
            </div>
            <div class="form-group">
                <label class="sr-only" for="status">状态</label>
                <select class="form-control" style="width: 200px;" name="status" id="status">
                    <option value="">== 请选择受理状态 ==</option>
                    <option @if(request()->get('status') == 1) selected @endif value="1">待受理</option>
                    <option @if(request()->get('status') == 2) selected @endif value="2">受理中</option>
                    <option @if(request()->get('status') == 3) selected @endif value="3">拒绝受理</option>
                    <option @if(request()->get('status') == 4) selected @endif value="4">已完成</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">查询</button>
            <a href="{{ route('admin.invoice.apply_list') }}" class="btn btn-default">重置</a>
        </form>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>编号</th>
            <th>单位名称</th>
            <th>供应商</th>
            <th>税务登记号</th>
            <th>接收人</th>
            <th>状态</th>
            <th>提交时间</th>
            <th>受理时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
            @foreach($invoices as $index => $invoice)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $invoice->rise }}</td>
                    <td>{{ $invoice->tenat->name }}</td>
                    <td>{{ $invoice->tax_number }}</td>
                    <td>{{ $invoice->addressee }}</td>
                    <td>
                        @if($invoice->status == 1)
                            <span class="label label-default">待受理</span>
                        @elseif($invoice->status == 2)
                            <span class="label label-warning">受理中</span>
                        @elseif($invoice->status == 3)
                            <span class="label label-danger">拒绝受理</span>
                        @else
                            <span class="label label-success">已完成</span>
                        @endif
                    </td>
                    <td>{{ $invoice->created_at }}</td>
                    <td>{{ $invoice->handle_time }}</td>
                    <td>
                        <a href="{{ route('admin.invoice.show', ['id' => $invoice->id]) }}" class="btn btn-primary btn-xs">详情</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-5">
    {!! $invoices->render() !!}
</div>
