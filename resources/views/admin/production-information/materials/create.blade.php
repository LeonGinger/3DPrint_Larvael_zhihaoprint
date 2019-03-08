<div class="row">
    <div class="col-md-6">
        @include('admin.shared._errors')
        <form class="form-horizontal" method="post" action="{{ route('admin.materials.store') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">名称</label>
                <div class="col-sm-10">
                    <input type="text" value="" class="form-control" name="name" id="name" placeholder="输入名称">
                </div>
            </div>
            <div class="form-group">
                <label for="price" class="col-sm-2 control-label">价格</label>
                <div class="col-sm-10">
                    <input type="text" value="" class="form-control" name="price" id="price" placeholder="输入价格">
                </div>
            </div>
            <div class="form-group">
                <label for="density" class="col-sm-2 control-label">密度</label>
                <div class="col-sm-10">
                    <input type="text" value="" class="form-control" name="density" id="density" placeholder="输入密度">
                </div>
            </div>
            <div class="form-group">
                <label for="shape" class="col-sm-2 control-label">材质形态</label>
                <div class="col-sm-10">
                    <select name="shape" class="form-control" id="shape">
                        <option value="粉体">粉体</option>
                        <option value="固体">固体</option>
                        <option value="液体">液体</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="mold_id" class="col-sm-2 control-label">制造工艺</label>
                <div class="col-sm-10">
                    <select name="mold_id" class="form-control" id="mold_id">
                        @foreach($moldings as $molding)
                            <option value="{{ $molding->id }}">{{ $molding->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="tenat_id" class="col-sm-2 control-label">供应商名称</label>
                <div class="col-sm-10">
                    <select name="tenat_id" class="form-control" id="tenat_id">
                        <option value="0">请选择供应商</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="status" class="col-sm-2 control-label">状态</label>
                <div class="col-sm-10">
                    <select name="status" class="form-control" id="status">
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">立即提交</button>
                    <a type="submit" href="{{ route('admin.materials.index') }}" class="btn btn-default" style="margin-left: 10px;">返回列表</a>
                </div>
            </div>
        </form>
    </div>
</div>
