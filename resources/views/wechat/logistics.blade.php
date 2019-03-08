<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		@include('wechat.common.title')
		<meta name="viewport" content="initial-scale=1, maximum-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">
		<link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css"></head>
	<body>
		<header class="bar bar-nav">
			<a class="button button-link button-nav pull-left back" href="/customer/orderlist">
				<span class="icon icon-left"></span>返回</a>
			<h1 class="title">送货单列表</h1></header>@include('wechat.common.navbar')
		<div class="content">
			@if(empty($data['val']))
			<div class="content-block">
				<p>该订单还未发货</p>
			</div>
			@endif
			@foreach ($data['val'] as $item)
			<div class="card" id="{{ $item['no'] }}">
				<div class="card-header">
					<span>送货单编号 : {{ $item['no'] }}</span></div>
				<div class="card-content">
					<div class="list-block media-list" style="font-size:.7rem">
						<ul>
							<li class="item-content">
								<div class="item-inner">@if ($item['distribution_mode'] == '0')
									<div class="item-title-row">快递送货</div>
									<div class="item-title-row color-gray" style="font-size:.7rem">
										<div>{{$item['express_name']}} {{$item['express_no']}}</div>
										<div>@if ($item['status'] == '0') 已发货 @elseif ($item['status'] == '1') 已收货 @else 完成 @endif</div></div>@elseif ($item['distribution_mode'] == '1')
									<div class="item-title-row">送货</div>@elseif ($item['distribution_mode'] == '2')
									<div class="item-title-row">客户自提</div>@endif</div></li>
						</ul>
					</div>
					<div class="list-block media-list">
						<ul>
							@foreach ($item['parts'] as $pi)
							<li class="item-content">
								<div class="item-media">
									<img src="{{ $pi['diagram'] }}" data-title="{{ $pi['name'] }}" class="pbimg" width="44"></div>
								<div class="item-inner">
									<div class="item-title-row">
										<div class="item-title">{{ $pi['name'] }}</div>
										<div style="font-size:.7rem">@if ($pi['status']<=3 ) 已分批送货 @elseif ($pi[ 'status']==4 ) 送货完成 @else 完成 @endif </div></div>
										<div class="item-title-row color-gray" style="font-size:.7rem">
											<div>
												<span style="padding-right:10px">总数</span>{{ $pi['product_num'] }}件</div>
											<div>
												<span style="padding-right:10px">送货数</span>{{ $pi['yscount'] }}件</div></div>
									</div>
							</li>
							@endforeach
						</ul>
						</div>
					</div>
					<div class="card-footer">
						<span>{{ date('Y-m-d',strtotime($item['created_at'])) }}</span>
						<div class="row">@if ($item['status'] == '0')
							<a href="javascript:;" data-cfurl="{{$item['confirm']}}" class="weui-btn_primary">确认收货</a>&nbsp;&nbsp; @endif
							<a href="javascript:;" data-excode="{{$item['express_code']}}" data-exno="{{$item['express_no']}}" class="weui-btn_default">查看物流</a></div>
					</div>
				</div>
				@endforeach
			</div>
			<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
			<script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
			<script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
			<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
			<script type="text/javascript" charset="utf-8">wx.config(@json($data['jssdk']));
				$('#dialogsFalse1').click(function() {
					wx.closeWindow();
				});
				$(function() {
					$('.item-title').each(function(){
						var str = $(this).html();
						var reg = RegExp(/重做/);
						if(str.match(reg)){
							$(this).css('color','red');
						}
					});
					$('.pbimg').click(function() {
						var pbimg = $.photoBrowser({
							photos: [{
								url: $(this).attr('src'),
								caption: $(this).data('title')
							}],
							type: 'popup'
						});
						pbimg.open();
					});
					$('.weui-btn_primary').click(function() {
						var cbut = $(this);
						var url = '{{ url("/api/wechat/depot/confirm") }}';
						$.confirm('请确认您是否已经收到货物', '确认收货？',
						function() {
							$.showPreloader('正在执行请稍后');
							$.ajax({
								type: 'POST',
								url: url,
								data: {
									val: cbut.data('cfurl')
								},
								dataType: 'json',
								success: function(data) {
									$.hidePreloader();
									if(data['redeiurl'] != ''){
										$.alert('您的零件已经全部收货，订单已经完成，请您评价本次服务~', '零件已收货', function () {
											location.href=data['redeiurl'];
										});
									}else{
										$.alert('已确认收货');
										location.reload();
									}
								},
								error: function(xhr, type, error) {
									$.hidePreloader();
									$.toast("操作失败");
								}
							});
						});
					});
					$('.weui-btn_default').click(function() {
						$.showPreloader('正在查询物流信息');
						var url = '{{ url("/api/express/chech") }}';
						$.ajax({
							type: 'POST',
							url: url,
							data: {
								shipperCode: $(this).data('excode'),
								logisticCode: $(this).data('exno')
							},
							dataType: 'json',
							success: function(data) {
								$.hidePreloader();
								var outex = '';
								if (data == '' || data == "无物流信息") {
									$.toast("暂无物流信息，请稍后再试。");
									return;
								} else {
									$.each(data,
									function(i, n) {
										outex = (n['AcceptTime'] + "<br/>" + n['AcceptStation'] + "<br/><br/>") + outex;
									});
								}
								console.log(outex);
								$('#outtex').html(outex);
								$.popup('.popup-wuliu');
							},
							error: function(xhr, type, error) {
								$.hidePreloader();
								$.toast("暂无物流信息，请稍后再试。");
							}
						})
					});
				});</script>
			<div class="popup popup-wuliu">
				<div class="content-block close-popup">
					<h4>物流信息</h4>
					<p id="outtex" style="font-size:.7rem"></p>
					<p>
						<a href="#" class="button close-popup">关闭</a></p>
				</div>
			</div>
	</body>

</html>