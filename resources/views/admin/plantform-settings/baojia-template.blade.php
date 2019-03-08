<div class="row center-block">
    <form action="{{ route('admin.plantform_settings.baojia.save') }}" method="post">
        {{ csrf_field() }}
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">零件检验质量标准模板文字</h3>
                </div>
                <div class="panel-body">
                    <div class="temp_baojia_template_text1" style="display: none;">{{ $plant_form_setting['baojia_template_text1'] }}</div>
                    <div class="col-sm-10" id="container-wrapper1">
                        <script id="baojia_template_text1" name="baojia_template_text1" type="text/plain"></script>
                        <script>
                            var ue1 = UE.getEditor('baojia_template_text1');
                            ue1.ready(function() {
                                ue1.execCommand('insertHtml', $('.temp_baojia_template_text1').html())
                                ue1.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">货物交付以及结算区域模板文字</h3>
                </div>
                <div class="panel-body">
                    <div class="temp_baojia_template_text2" style="display: none;">{{ $plant_form_setting['baojia_template_text2'] }}</div>
                    <div class="col-sm-10" id="container-wrapper2">
                        <script id="baojia_template_text2" name="baojia_template_text2" type="text/plain"></script>
                        <script>
                            var ue2 = UE.getEditor('baojia_template_text2');
                            ue2.ready(function() {
                                ue2.execCommand('insertHtml', $('.temp_baojia_template_text2').html())
                                ue2.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12" style="text-align: center">
            <button type="submit" class="btn btn-primary">提交</button>
        </div>
    </form>
</div>
