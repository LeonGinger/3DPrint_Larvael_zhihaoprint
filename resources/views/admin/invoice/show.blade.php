<div class="panel panel-default">
    <div class="panel-heading">
        <span>单位信息</span>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-md-12">单位名称：{{ $invoice->tenat->name }}</div>
            <div class="col-xs-12 col-md-12">纳税人识别号：{{ $invoice->tax_number }}</div>
            <div class="col-xs-12 col-md-12">开户银行：{{ $invoice->base_account_bank }}</div>
            <div class="col-xs-12 col-md-12">银行账号：{{ $invoice->base_account_number }}</div>
            <div class="col-xs-12 col-md-12">单位地址：{{ $invoice->tenat->province }}{{ $invoice->tenat->city }}{{ $invoice->tenat->region }}{{ $invoice->tenat->address }}</div>
            <div class="col-xs-12 col-md-12">单位电话：{{ $invoice->tenat->phone }}</div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <span>发票收件人信息</span>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-md-12">联系人：{{ $invoice->addressee }}</div>
            <div class="col-xs-12 col-md-12">电话：{{ $invoice->phone }}</div>
            <div class="col-xs-12 col-md-12">详细地址：{{ $invoice->province }}{{ $invoice->city }}{{ $invoice->region }}{{ $invoice->address }}</div>
        </div>
    </div>
</div>
<div class="panel panel-primary">
    <div class="panel-heading">
        <span>更新状态</span>
    </div>
    <div class="panel-body">
        @include('admin.shared._errors')
        <form class="form-horizontal" method="post" enctype="multipart/form-data" action="{{ route('admin.invoice.update', ['id' => $invoice->id]) }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="status" class="col-sm-2 control-label">状态</label>
                <div class="col-sm-10">
                    <select name="status" class="form-control" id="status">
                        <option @if($invoice->status == 1) selected @endif value="1">待受理</option>
                        <option @if($invoice->status == 2) selected @endif value="2">受理中</option>
                        <option @if($invoice->status == 3) selected @endif value="3">拒绝受理</option>
                        <option @if($invoice->status == 4) selected @endif value="4">已完成</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="feed_file" class="col-sm-2 control-label">上传文件</label>
                <div class="col-sm-10">
                    <input type="file" name="feed_file" id="feed_file" class="form-control">
                    <span class="help-block">上传电子发票文档等文件。</span>
                    @if($invoice->file_url)
                        <span class="help-block">已上传文件：<a href="{{ $invoice->file_url }}" target="_blank">点击查看</a> </span>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="feedback" class="col-sm-2 control-label">受理回复</label>
                <div class="col-sm-10">
                    <textarea name="feedback" class="form-control" id="feedback" cols="30" rows="5">{{ $invoice->feedback }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">立即提交</button>
                    <a href="{{ route('admin.invoice.apply_list') }}" class="btn btn-default" style="margin-left: 10px;">返回列表</a>
                </div>
            </div>
        </form>

    </div>
</div>
