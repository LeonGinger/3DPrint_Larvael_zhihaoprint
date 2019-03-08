<div class="table-responsive">
    <a href="{{ route('admin.molding_processes.create') }}" class="btn btn-success" style="margin-bottom: 10px;">添加生产工艺</a>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>编号</th>
                <th>工艺名称</th>
                <th>供应商</th>
                <th>状态</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($molding_processes as $molding_process)
                <tr>
                    <td>{{ $molding_process->id }}</td>
                    <td>{{ $molding_process->name }}</td>
                    <td>{{ $molding_process->tenant ? $molding_process->tenant->name : '--' }}</td>
                    <td>
                        @if($molding_process->status)
                            <span class="text-success">启用</span>
                        @else
                            <span class="text-danger">禁用</span>
                        @endif
                    </td>
                    <td>{{ $molding_process->created_at }}</td>
                    <td>{{ $molding_process->updated_at }}</td>
                    <td>
                        <a href="{{ route('admin.molding_processes.edit', ['id' => $molding_process->id]) }}" class="btn btn-primary btn-xs">编辑</a>
                        <form style="display: inline;" method="post" action="{{ route('admin.molding_processes.destroy', ['id' => $molding_process->id]) }}">
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
    {!! $molding_processes->render() !!}
</div>
