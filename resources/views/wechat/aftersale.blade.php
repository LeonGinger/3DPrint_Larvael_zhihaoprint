<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		@include('wechat.common.title')
		<meta name="viewport" content="initial-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<link rel="stylesheet" href="/css/sm.min.css">
		<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">

	</head>
	<body>
		<header class="bar bar-nav">
			<a class="button button-link button-nav pull-left back" href="/customer/orderlist">
			<span class="icon icon-left"></span>返回</a>
			<h1 class="title">售后服务</h1>
		</header>
		@include('wechat.common.navbar')
		<div class="content">
			<div class="content-block-title">订单信息</div>
			<div class="list-block media-list">
				<ul>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">订单编号：{{$data['val']['no']}}</div>
						</div>
					</div>
				</li>
				<div style="display:none">
				@foreach ($data['val']['parts'] as $part)
				<li>
					<div class="item-content">
						<div class="item-media"><img src="{{ $part['diagram'] }}" data-title="{{ $part['name'] }}" class="pbimg" style="width: 2.2rem;"></div>
						<div class="item-inner">
							<div class="item-title-row">
								<div class="item-title">{{ $part['name'] }}</div>
							</div>
							<div class="item-title-row color-gray" style="font-size:.7rem">
								<div class="item-pr10"><span>{{ $part['matname'] }}</span><span>{{ $part['modname'] }}</span><span>{{ $part['surname'] }}</span><span>{{ $part['equname'] }}</span></div>
								<span>{{ $part['product_num'] }}件</span>
							</div>
						</div>
					</div>
				</li>
				@endforeach
				</div>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title-row">
								<div class="item-title color-gray" style="font-size:.7rem;font-weight:normal !important;">{{date('Y-m-d',strtotime($data['val']['crdate']))}}</div>
								<div class="item-after" style="font-size:.7rem">
									@if($data['val']['status'] == 0)
										审核中
									@elseif($data['val']['status'] == 1)
										确认报价单
									@elseif($data['val']['status'] == 2)
										准备生产
									@elseif($data['val']['status'] == 3)
										生产中
									@elseif($data['val']['status'] == 4)
										生产完成
									@elseif($data['val']['status'] == 5)
										已发货
									@elseif($data['val']['status'] == 6)
										已完成
									@endif
								</div>
							</div>
						</div>
					</div>
				</li>
				</ul>
			</div>
			
			<div class="content-block">
				@if($data['val']['status'] <= 1 )
					<a href="javascript:;" id="submit" class="button button-big button-fill">申请退单</a>
				@elseif($data['val']['status'] >= 5 )
					<a href="javascript:;" id="submit" class="button button-big button-fill">申请零件重做</a>
				@else
					订单生产中，暂时不能申请售后，请等待订单完成
				@endif
			</div>
			@if(empty($data['val']['aftsla']))
			@else
			<div class="content-block-title">售后记录</div>
				@foreach ($data['val']['aftsla'] as $afts)
			<div class="list-block media-list">
				<ul>
					<li>
						<div class="item-content">
							<div class="item-inner">
							<div class="item-title-row">
							<div class="item-title">
								@if($afts['returned_type'] ==0)
									取消订单
								@else
									零件重做
								@endif
							</div>
							<div class="item-after">
								@if($afts['status'] == 0)
									审核中
								@elseif($afts['status'] == 1 )
									审核通过
								@else
									不通过
								@endif
							</div>
							</div>
							<div class="item-subtitle">原因</div>
							<div class="item-text">
								@if(empty($afts['remark']))
									未填写
								@else
									{{$afts['remark']}}
								@endif
							</div>
								<div class="item-title-row color-gray" style="font-size:.7rem">
									<div>{{date('Y-m-d',strtotime($afts['created_at']))}}</div>
									@if($afts['status'] < 1)
									<div><a href="javascript:;" data-odrd="{{$afts['canid']}}" id="cancle" class="cancle">撤回申请</a></div>
									@elseif(!empty($afts['parts']))
										<div><a href="{{url('/wechat/order/procuplan').'?val='.$data['oid']}}">生产计划</a>&nbsp;&nbsp;&nbsp;<a href="{{url('/wechat/order/logistics').'?val='.$data['oid']}}" >查看物流</a></div>
									@endif
								</div>
							</div>
						</div>
						@if(!empty($afts['parts']))
						@foreach ($afts['parts'] as $part)
							<div class="item-content">
								<div class="item-media"><img src="{{ $part['diagram'] }}" data-title="{{ $part['name'] }}" class="pbimg" style="width: 2.2rem;"></div>
								<div class="item-inner">
									<div class="item-title-row">
										<div class="item-title">{{ $part['name'] }}</div>
									</div>
									<div class="item-title-row color-gray" style="font-size:.7rem">
										<span>重做数量 {{ $part['product_num'] }} 件</span>
									</div>
								</div>
							</div>
						@endforeach
						@endif
					</li>
				
				</ul>
			</div>
				@endforeach
			@endif
		</div>
		<div class="popup popup-repro">
		<div class="content-block">
			<div class="content-block-title">选择要重新生产的零件</div>
			<form action="" method="post" id="postfrom">
			<div class="list-block media-list">
				<ul>
					@foreach ($data['val']['parts'] as $part)
					<li>
						<label class="label-checkbox item-content">
							<div class="item-inner">
								<div class="item-title-row">
									<div class="item-title">{{ $part['name'] }}({{ $part['product_num'] }}件)</div>
									<div class="item-after"><input type="number" class="innum" style="width: 100px;border: 1px solid #ccc;height: 25px;font-size:.7rem" name="pid[{{ $part['id'] }}]" placeholder='输入重做数量' data-oldnum="{{ $part['product_num'] }}" />/件</div>
								</div>
							</div>
						</label>
					</li>
					@endforeach
					<li class="align-top">
						<div class="item-content">
							<div class="item-inner">
								<div class="item-title label">重做原因</div>
								<div class="item-input">
									<textarea id="remark" name="remark" placeholder='请输入重做原因'></textarea>
								</div>
							</div>
						</div>
					</li>
				</ul>
				<input type="hidden" name="val" value="{{$data['oid']}}" >
			</div>
			</form>
			<div class="content-block">
				<div class="row">
					<div class="col-50"><a href="#" class="button button-big button-fill button-danger close-popup">取消</a></div>
					<div class="col-50"><a href="#" id="resub" class="button button-big button-fill button-success">提交</a></div>
				</div>
			</div>
		</div>
		</div>
		<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
		<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
			var pbimg = new Array();
			wx.config(@json($data['jssdk']));
			$('#dialogsFalse1').click(function(){
				wx.closeWindow();
			});
			$(function(){
				$('.innum').on('blur',function(i){
					var nownum = $(this).val();
					var oldnum = $(this).data('oldnum');
					if(nownum > oldnum){
						$(this).val(oldnum);
						//alert('');
					}
				});
				$('#resub').click(function(){
					if($('#remark').val() == ''){
						$.toast("请输入重做原因");
						return false;
					}
					$.confirm('您的零件将会重新开始生产', '确定重做？', function () {
						$.showPreloader('请稍等。。');
						var subdata = $('#postfrom').serializeArray();
						$.ajax({
							type: 'POST',
							url: '{{url('/api/wechat/order/return/rebuid')}}',
							data: subdata,
							dataType: 'json',
							success: function(data){
								$.hidePreloader();
								$.toast(data.message);
								window.location.reload();
							},
							error: function(xhr, type, error){
								console.log(xhr);
								alert('发生错误，请刷新重试。')
							}
						})
					});
				});
				$('.pbimg').each(function(i){
					$(this).click(function(){
						pbimg[i] = $.photoBrowser({photos : [{ url:$(this).attr('src'),caption: $(this).data('title')}],type: 'popup'});
						pbimg[i].open();
					});
				});
				$('.cancle').click(function(){
					var canbut = $(this);
					$.confirm('您正在撤销退单申请', '确定撤销？', function () {
						$.showPreloader('请稍等。。');
						$.ajax({
							type: 'POST',
							url: '{{url('/api/wechat/order/return/cancel')}}',
							data: {val: canbut.data('odrd')},
							dataType: 'json',
							success: function(data){
								$.hidePreloader();
								$.toast(data.message);
								window.location.reload();
							},
							error: function(xhr, type, error){
								$.hidePreloader();
								var ero = $.parseJSON(xhr.responseText);
								var etx = '';
								if(ero.errors != null){
									$.each(ero.errors, function(index, item){
										etx = etx + item + "<br/>";
									});
								}else
									etx = ero.message;
								$.alert(etx);
							}
						});
					});
				});
				$('#submit').click(function(){
					@if($data['val']['status'] <= 1 )
						$.prompt('请输入您取消订单的原因', function (value) {
							if(value == ''){$.toast("请输入您取消订单的原因");return false;}
							$.showPreloader('请稍等。。');
							$.ajax({
								type: 'POST',
								url: '{{url('/api/wechat/order/return')}}',
								data: {val: $('input[type=hidden]').val(),remark:value},
								dataType: 'json',
								success: function(data){
									$.hidePreloader();
									$.alert(data.message, function () {
										window.location.reload();
									});
								},
								error: function(xhr, type, error){
									$.hidePreloader();
									var ero = $.parseJSON(xhr.responseText);
									var etx = '';
									if(ero.errors != null){
										$.each(ero.errors, function(index, item){
											etx = etx + item + "<br/>";
										});
									}else
										etx = ero.message;
									$.alert(etx, '出错了');
								}
							});
						});
					@elseif($data['val']['status'] > 5 )
						$.popup('.popup-repro');
					@else
						$.alert('此订单已经开始生产，暂时不能申请售后服务。');
					@endif
				});
			});
		</script>
		<script>
		$('textarea').on('blur',function(){setTimeout(function(){window.scrollTo(0, 0)},100)});//解决微信6.7.4以上输入框关闭后页面空缺的问题
		</script>
	</body>
</html>