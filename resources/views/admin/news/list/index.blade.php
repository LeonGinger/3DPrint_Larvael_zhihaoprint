<div class="table-responsive">
    <a href="{{ route('admin.news.list.create') }}" class="btn btn-success" style="margin-bottom: 10px;">添加新闻</a>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>编号</th>
                <th>标题</th>
                <th>副标题</th>
                <th>作者</th>
                <th>浏览量</th>
                <th>是否显示</th>
                <th>排序</th>
                <th>创建时间</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($news_list as $news_item)
                <tr>
                    <td>{{ $news_item->id }}</td>
                    <td>{{ $news_item->title }}</td>
                    <td>{{ $news_item->sub_title }}</td>
                    <td>{{ $news_item->author }}</td>
                    <td>{{ $news_item->view_count }}</td>
                    <td>
                        @if($news_item->is_show)
                            <span class="label label-success">是</span>
                        @else
                            <span class="label label-danger">否</span>
                        @endif
                    </td>
                    <td>{{ $news_item->sort }}</td>
                    <td>{{ $news_item->created_at }}</td>
                    <td>{{ $news_item->updated_at }}</td>
                    <td>
                        <a href="{{ route('admin.news.list.edit', ['id' => $news_item->id]) }}" class="btn btn-primary btn-xs">编辑</a>
                        <form style="display: inline;" method="post" action="{{ route('admin.news.list.destroy', ['id' => $news_item->id]) }}">
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
