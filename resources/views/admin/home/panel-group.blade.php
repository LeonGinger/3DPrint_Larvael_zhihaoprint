<div class="row center-block">
    <div class="col-md-4 col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <h3 class="panel-title">当前商家数量</h3>
            </div>
            <a href="{{ route('admin.tenant.list', ['type' => 'all']) }}">
                <div class="panel-body">20</div>
            </a>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="panel-title">新入驻商家</h3>
            </div>
            <a href="{{ route('admin.tenant.list', ['type' => 'new']) }}">
                <div class="panel-body">17</div>
            </a>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title">已过期商家</h3>
            </div>
            <a href="{{ route('admin.tenant.list', ['type' => 'expired']) }}">
                <div class="panel-body">3</div>
            </a>
        </div>
    </div>
</div>