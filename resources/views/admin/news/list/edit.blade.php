<div class="row">
    <div class="col-md-6">
        @include('admin.shared._errors')
        <form class="form-horizontal" method="post" action="{{ route('admin.news.list.store') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="title" class="col-sm-2 control-label">标题</label>
                <div class="col-sm-10">
                    <input type="text" value="{{ $news->title }}" required class="form-control" name="title" id="title" placeholder="输入新闻标题">
                </div>
            </div>
            <div class="form-group">
                <label for="sub_title" class="col-sm-2 control-label">副标题</label>
                <div class="col-sm-10">
                    <input type="text" value="{{ $news->sub_title }}" class="form-control" name="sub_title" id="sub_title" placeholder="输入新闻副标题">
                </div>
            </div>
            <div class="form-group">
                <label for="news_type_id" class="col-sm-2 control-label">新闻类别</label>
                <div class="col-sm-10">
                    <select class="form-control" name="news_type_id" id="news_type_id">
                        @foreach($news_types as $news_type)
                            <option @if($news->news_type_id == $news_type->id) selected @endif value="{{ $news_type->id }}">{{ $news_type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="author" class="col-sm-2 control-label">作者</label>
                <div class="col-sm-10">
                    <input type="text" value="{{ $news->author }}" class="form-control" name="author" id="author" placeholder="输入新闻作者">
                </div>
            </div>
            <div class="form-group">
                <label for="views_count" class="col-sm-2 control-label">浏览量</label>
                <div class="col-sm-10">
                    <input type="number" value="{{ $news->view_count }}" min="0" class="form-control" name="views_count" id="views_count" placeholder="输入浏览量">
                    <span class="help-block">用户端展示的浏览量</span>
                </div>
            </div>
            <div class="form-group">
                <label for="is_show" class="col-sm-2 control-label">是否显示</label>
                <div class="col-sm-10">
                    <select name="is_show" class="form-control" id="is_show">
                        <option @if($news->is_show) selected @endif value="1">是</option>
                        <option @if(!$news->is_show) selected @endif value="0">否</option>
                    </select>
                    <span class="help-block">选择否用户端不显示该新闻</span>
                </div>
            </div>
            <div class="form-group">
                <label for="sort" class="col-sm-2 control-label">排序</label>
                <div class="col-sm-10">
                    <input type="number" value="{{ $news->sort }}" class="form-control" name="sort" id="sort" placeholder="输入排序">
                    <span class="help-block">排序数字 1 ~ 100，越大越靠前。不填以创建时间排序</span>
                </div>
            </div>
            <div class="form-group">
                <label for="container" class="col-sm-2 control-label">内容</label>
                <div class="temp-content" style="display: none;">{{ $news->content }}</div>
                <div class="col-sm-10" id="container-wrapper">
                    <script id="container" name="content" type="text/plain"></script>
                    <script>
                        var ue = UE.getEditor('container');
                        ue.ready(function() {
                          ue.execCommand('insertHtml', $('.temp-content').html())
                          ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                        });
                    </script>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">立即提交</button>
                    <button type="button" style="margin-left: 10px;" onclick="window.location.reload()" class="btn btn-info">刷新页面</button>
                    <a type="submit" href="{{ route('admin.news.list') }}" class="btn btn-default" style="margin-left: 10px;">返回列表</a>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- 实例化编辑器 -->
{{--<script type="text/javascript">--}}
  {{--$(function() {--}}
    {{--// var script = document.createElement('script');--}}
    {{--// script.type = 'text/plain';--}}
    {{--// script.name = 'content';--}}
    {{--// script.id = 'container';--}}
    {{--var script = $('<script id="container" name="content" type="text/plain"><\/script>');--}}
    {{--$('#container-wrapper').append(script);--}}

    {{--console.log('12321')--}}
  {{--})--}}
{{--</script>--}}

<!-- 编辑器容器 -->
{{--<script id="container" name="content" type="text/plain"></script>--}}

