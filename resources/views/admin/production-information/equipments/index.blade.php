<div class="table-responsive">
    <a href="{{ route('admin.equipments.create') }}" class="btn btn-success" style="margin-bottom: 10px;">添加生产设备</a>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>编号</th>
            <th>供应商</th>
            <th>生产工艺名称</th>
            <th>设备型号</th>
            <th>成型范围</th>
            <th>制造商名称</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($equipments as $equipment)
            <tr>
                <td>{{ $equipment->id }}</td>
                <td>{{ $equipment->tenant ? $equipment->tenant->name : '--' }}</td>
                <td>{{ $equipment->molding->name }}</td>
                <td>{{ $equipment->mname }}</td>
                <td>{{ $equipment->marc }}</td>
                <td>{{ $equipment->mmaker }}</td>
                <td>
                    @if($equipment->status)
                        <span class="text-success">启用</span>
                    @else
                        <span class="text-danger">禁用</span>
                    @endif
                </td>
                <td>{{ $equipment->created_at }}</td>
                <td>{{ $equipment->updated_at }}</td>
                <td>
                    <a href="{{ route('admin.equipments.edit', ['id' => $equipment->id]) }}" class="btn btn-primary btn-xs">编辑</a>
                    <form style="display: inline;" method="post" action="{{ route('admin.equipments.destroy', ['id' => $equipment->id]) }}">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button onclick="return confirm('确定要删除吗？')" type="submit" class="btn btn-default btn-xs">删除</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-5">
    {!! $equipments->render() !!}
</div>
