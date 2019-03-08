<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    @include('wechat.common.title')
	    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
	    <link rel="shortcut icon" href="/favicon.ico">
	    <meta name="apple-mobile-web-app-capable" content="yes">
	    <meta name="apple-mobile-web-app-status-bar-style" content="black">
	    <link rel="stylesheet" href="/css/sm.min.css">
	    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
		<style>.item-pr10 span{padding-right:10px;display:inline-block}</style>
		<script>
			var okpicjn = new Array();
		</script>
	</head>
	<body>
	<div class="page-group">
		<div class="page page-current">
			<header class="bar bar-nav">
				<a class="button button-link button-nav pull-left back" href="/customer/orderlist#{{$data['no']}}">
					<span class="icon icon-left"></span>返回
				</a>
				<h1 class="title">生产计划</h1>
			</header>
			@include('wechat.common.navbar')
		<div class="content native-scroll">
		@foreach ($data['val'] as $item)
			
			<div class="card">
				<div class="card-header" style="padding:0">
					<div class="list-block media-list">
						<ul>
						<li class="item-content">
						<div class="item-media">
						<img src="{{ $item['diagram'] }}" data-title="{{ $item['name'] }}" class="pbimg" width="44">
						</div>
						<div class="item-inner">
						<div class="item-title-row">
						<div class="item-title">
							{{ $item['name'] }}
						</div>
						</div>
						<div class="item-pr10 color-gray" style="font-size:.7rem"><span>{{ $item['matname'] }}</span><span>{{ $item['modname'] }}</span><span>{{ $item['surname'] }}</span><span>{{ $item['equname'] }}</span></div>
						</div>
						</li>
						</ul>
					</div>
				</div>
				<div class="card-content">
					<div class="list-block media-list" style="font-size:.7rem">
						<ul>
							<li class="item-content">
								<div class="item-inner">
								<?php $vs = json_decode($item['volume_size'],true); ?>
								<span class="color-gray">尺寸：{{ $vs['xx'] }}*{{ $vs['yy'] }}*{{ $vs['zz'] }}&nbsp;&nbsp;体积：{{ $vs['volume'] }}mm³</span>
								</div>
							</li>
							<li class="item-content">
								<div class="item-inner">
									<div class="item-title-row">订单日期</div>
									<div class="item-title-row color-gray" style="font-size:.7rem">
										<div><span style="padding-right:10px">启动</span>{{ date('Y-m-d',strtotime($item['start_date'])) }}</div>
										<div><span style="padding-right:10px">交货</span>{{ date('Y-m-d',strtotime($item['due_date'])) }}</div>
									</div>
								</div>
							</li>
							@if (!empty($item['plan']))
							<li class="item-content">
								<div class="item-inner">
									<div class="item-title-row">计划生产日期</div>
									<div class="item-title-row color-gray" style="font-size:.7rem">
										<div><span style="padding-right:10px">开始</span>@if(!empty($item['plan']['start_data'])){{ date('Y-m-d',strtotime($item['plan']['start_data'])) }}@else 未设置 @endif</div>
										<div><span style="padding-right:10px">完成</span>@if(!empty($item['plan']['end_data'])){{ date('Y-m-d',strtotime($item['plan']['end_data'])) }}@else 未设置 @endif</div>
									</div>
								</div>
							</li>
							<li class="item-content">
								<div class="item-inner">
									<div class="item-title-row">实际生产日期</div>
									<div class="item-title-row color-gray" style="font-size:.7rem">
										<div><span style="padding-right:10px">开始</span>
										<span>@if(!empty($item['plan']['real_sdate'])){{ date('Y-m-d',strtotime($item['plan']['real_sdate'])) }}@else 未设置 @endif</span></div>
										<div>@if(!empty($item['plan']['real_edate']))<span style="padding-right:10px">完成</span>{{ date('Y-m-d',strtotime($item['plan']['real_edate'])) }}@else 生产未完成 @endif</div>
									</div>
								</div>
							</li>
							@else
							<li class="item-content">
								<div class="item-inner">
								<span class="color-gray">尚未制订生产计划</span>
								</div>
							</li>
							@endif
						</ul>
					</div>
				</div>
				<div class="card-footer">
					<span>
					@if ($item['plan']['status'] == '2')
									生产完成
								@elseif ($item['plan']['status'] == '1')
									生产中
								@else
									等待生产
								@endif
								</span>
								<span>{{ $item['product_num'] }}件</span>
@if (!empty($item['plan']['pics']))
<script type="text/javascript" charset="utf-8">
	okpicjn[{{$item['plan']['id']}}] = {photos : <?php echo $item['plan']['pics']; ?>,type: 'popup'};
</script>
								<a href="javascript:;" class="okpic" data-picinx="{{$item['plan']['id']}}">查看完工图</a>
								@else
									<span>暂无完工图</span>
@endif
				</div>
			</div>
		@endforeach
		</div>
		<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='/we-ui/js/sm-extend.min.js' charset='utf-8'></script>
		<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
    wx.config(@json($data['jssdk']));
	$('#dialogsFalse1').click(function(){
		wx.closeWindow();
	});

$(function(){
	$('.okpic').click(function(){
		var picinx = $(this).data('picinx');
		var okpic = $.photoBrowser(okpicjn[picinx]);
		okpic.open();
	});
	$('.pbimg').click(function(){
		var pbimg = $.photoBrowser({photos : [{ url:$(this).attr('src'),caption: $(this).data('title')}],type: 'popup'});
		pbimg.open();
	});
	$('.item-title').each(function(){
		var str = $(this).html();
		var reg = RegExp(/重做/);
		if(str.match(reg)){
			$(this).css('color','red');
		}
	});
});
		</script>

		</div>
	</div>
	</body>
</html>
