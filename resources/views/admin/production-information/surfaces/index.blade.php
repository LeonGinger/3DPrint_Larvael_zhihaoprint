<div class="table-responsive">
    <a href="{{ route('admin.surfaces.create') }}" class="btn btn-success" style="margin-bottom: 10px;">添加表面处理</a>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>编号</th>
            <th>名称</th>
            <th>供应商</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>更新时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($surfaces as $surface)
            <tr>
                <td>{{ $surface->id }}</td>
                <td>{{ $surface->name }}</td>
                <td>{{ $surface->tenant ? $surface->tenant->name : '--' }}</td>
                <td>
                    @if($surface->status)
                        <span class="text-success">启用</span>
                    @else
                        <span class="text-danger">禁用</span>
                    @endif
                </td>
                <td>{{ $surface->created_at }}</td>
                <td>{{ $surface->updated_at }}</td>
                <td>
                    <a href="{{ route('admin.surfaces.edit', ['id' => $surface->id]) }}" class="btn btn-primary btn-xs">编辑</a>
                    <form style="display: inline;" method="post" action="{{ route('admin.surfaces.destroy', ['id' => $surface->id]) }}">
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
    {!! $surfaces->render() !!}
</div>
