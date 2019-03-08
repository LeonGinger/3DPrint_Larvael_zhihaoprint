<div class="row center-block">
    <form action="{{ route('admin.plantform_settings.ad.save') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="col-md-6 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">页眉广告图片</h3>
                </div>
                <div class="panel-body">
                    <img width="100%" height="100px" src="{{ $plant_form_setting['ad_header_url'] ? $plant_form_setting['ad_header_url'] : 'http://placehold.it/360x100/0f0/ccc.png' }}" alt="" class="img-rounded">
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="adHeader">{{ $plant_form_setting['ad_header_url'] ? '点击更换图片' : '点击上传图片' }}</label>
                        <input name="ad_header_image" type="file" id="adHeader">
                        <p class="help-block">图片尺寸：160 x 30</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">页脚广告图片</h3>
                </div>
                <div class="panel-body">
                    <img width="100%" height="100px" src="{{ $plant_form_setting['ad_footer_url'] ? $plant_form_setting['ad_footer_url'] : 'http://placehold.it/360x100' }}" alt="" class="img-rounded">
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="adFooter">{{ $plant_form_setting['ad_footer_url'] ? '点击更换图片' : '点击上传图片' }}</label>
                        <input name="ad_footer_image" type="file" id="adFooter">
                        <p class="help-block">图片尺寸：160 x 30</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12" style="text-align: center">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </form>
</div>
