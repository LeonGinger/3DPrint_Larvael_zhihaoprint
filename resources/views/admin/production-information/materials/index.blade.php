<div class="table-responsive">
    <a href="{{ route('admin.materials.create') }}" class="btn btn-success" style="margin-bottom: 10px;">添加成型材料</a>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>编号</th>
                <th>名称</th>
                <th>供应商</th>
                <th>生产工艺名称</th>
                <th>价格</th>
                <th>密度</th>
                <th>材质形态</th>
                <th>状态</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        @foreach($materials as $index => $material)
            <tr>
                <td>{{ $material->id }}</td>
                <td>{{ $material->name }}</td>
                <td>{{ $material->tenant ? $material->tenant->name : '--' }}</td>
                <td>{{ $material->molding ? $material->molding->name : '--' }}</td>
                <td>¥{{ $material->price }}</td>
                <td>{{ $material->density }}</td>
                <td>{{ $material->shape }}</td>
                <td>
                    @if($material->status)
                        <span class="text-success">启用</span>
                    @else
                        <span class="text-danger">禁用</span>
                    @endif
                </td>
                <td>{{ $material->created_at }}</td>
                <td>{{ $material->updated_at }}</td>
                <td>
                    <a href="{{ route('admin.materials.edit', ['id' => $material->id]) }}" class="btn btn-primary btn-xs">编辑</a>
                    <form style="display: inline;" method="post" action="{{ route('admin.materials.destroy', ['id' => $material->id]) }}">
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
    {!! $materials->render() !!}
</div>
