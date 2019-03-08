<div class="row">
    <div class="col-md-6">
        @include('admin.shared._errors')
        <form class="form-horizontal" method="post" action="{{ route('admin.equipments.store') }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="mname" class="col-sm-2 control-label">设备型号</label>
                <div class="col-sm-10">
                    <input type="text" value="" class="form-control" name="mname" id="mname" placeholder="输入设备型号">
                </div>
            </div>
            <div class="form-group">
                <label for="marc" class="col-sm-2 control-label">成型范围</label>
                <div class="col-sm-10">
                    <input type="text" value="" class="form-control" name="marc" id="marc" placeholder="输入成型范围">
                </div>
            </div>
            <div class="form-group">
                <label for="mmaker" class="col-sm-2 control-label">制造商名称</label>
                <div class="col-sm-10">
                    <input type="text" value="" class="form-control" name="mmaker" id="mmaker" placeholder="输入制造商名称">
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
                    <a type="submit" href="{{ route('admin.equipments.index') }}" class="btn btn-default" style="margin-left: 10px;">返回列表</a>
                </div>
            </div>
        </form>
    </div>
</div>
