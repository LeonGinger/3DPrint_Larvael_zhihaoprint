<div class="table-responsive">
    <a href="{{ route('admin.news.types.create') }}" class="btn btn-success" style="margin-bottom: 10px;">添加新闻类型</a>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>编号</th>
                <th>类型名称</th>
                <th>排序</th>
                <th>状态</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($news_types as $news_type)
                <tr>
                    <td>{{ $news_type->id }}</td>
                    <td>{{ $news_type->name }}</td>
                    <td>{{ $news_type->sort }}</td>
                    <td>
                        @if($news_type->is_enable)
                            <span class="label label-success">启用</span>
                        @else
                            <span class="label label-danger">禁用</span>
                        @endif
                    </td>
                    <td>{{ $news_type->created_at }}</td>
                    <td>{{ $news_type->updated_at }}</td>
                    <td>
                        <a href="{{ route('admin.news.types.edit', ['id' => $news_type->id]) }}" class="btn btn-primary btn-xs">编辑</a>
                        <form style="display: inline;" method="post" action="{{ route('admin.news.types.destroy', ['id' => $news_type->id]) }}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button onclick="return confirm('删除分类同时删除该分类下所有新闻，确认删除吗？')" type="submit" class="btn btn-default btn-xs">删除</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
