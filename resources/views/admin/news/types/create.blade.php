<div class="row">
    <div class="col-md-6">
        @include('admin.shared._errors')
        <form class="form-horizontal" method="post" action="{{ route('admin.news.types.store') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">类型名称</label>
                <div class="col-sm-10">
                    <input type="text" value="" required class="form-control" name="name" id="name" placeholder="输入新闻类型名称">
                </div>
            </div>
            <div class="form-group">
                <label for="is_enable" class="col-sm-2 control-label">状态</label>
                <div class="col-sm-10">
                    <select name="is_enable" class="form-control" id="is_enable">
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="sort" class="col-sm-2 control-label">排序</label>
                <div class="col-sm-10">
                    <input type="number" value="0" class="form-control" name="sort" id="sort" placeholder="输入排序">
                    <span class="help-block">排序数字 1 ~ 100，越大越靠前。不填以创建时间排序</span>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">立即提交</button>
                    <a type="submit" href="{{ route('admin.news.types') }}" class="btn btn-default" style="margin-left: 10px;">返回列表</a>
                </div>
            </div>
        </form>
    </div>
</div>
