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
    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
</head>
<body>
	<div class="page-group">
		<div class="page page-current">
			<header class="bar bar-nav">
				<a class="button button-link button-nav pull-left back" href="/customer/orderlist">
					<span class="icon icon-left"></span>返回					</a>
				<h1 class="title">设置</h1>
			</header>
			@include('wechat.common.navbar')
			<div class="content">
				<div class="list-block media-list">
					<ul>
						<li class="item-content">
							<div class="item-media"><img src="{{$data['user']['avatar']}}" style="width: 4rem;"></div>
							<div class="item-inner">
								<div class="item-title-row">
									<div class="item-title">{{$data['user']['nickname']}}</div>
									<div class="item-after" style="display:none"><span class="icon icon-share"></span></div>
								</div>
								<div class="item-subtitle">{{$data['cust']['name']}}<span class="color-gray" style="font-size:.7rem">({{$data['cust']['no']}})</span></div>
								<div class="item-text">
									{{$data['cust']['province']}} {{$data['cust']['city']}} {{$data['cust']['area']}}<br/>
									{{$data['cust']['address']}}
								</div>
							</div>
						</li>
						<li class="item-content">
							<div class="item-media"><i class="icon icon-phone"></i></div>
							<div class="item-inner">
								<div class="item-after">{{$data['cust']['tel']}}</div>
							</div>
						</li>
					</ul>
				</div>
									<div class="content-block-title">我的商家</div>
				<div class="list-block media-list">
					<ul>
					@foreach ($data['tans'] as $item)
						<li class="item-content">
							<div class="item-media"><i class="icon icon-card"></i></div>
							<div class="item-inner">
								<div class="item-after">{{$item['name']}}</div>
							</div>
						</li>
					@endforeach						</ul>
				</div>
				<div class="content-block-title">联系人</div>
				<div class="list-block media-list">
					<ul>
					@foreach ($data['cust']['lkmans'] as $item)
						@if ($item['lk_type'] == 0)
						<li>
							<a href="#" class="item-content">
								<div class="item-inner">
									<div class="item-title-row">
										<div class="item-title">{{$item['linkman_name']}}</div>
										<div class="item-after">{{$item['lk_phone']}}</div>
									</div>
									<div class="item-subtitle">{{$item['province']}} {{$item['city']}} {{$item['area']}}<br/>{{$item['lk_address']}}</div>
								</div>
							</a>
						</li>
						@endif
					@endforeach
					</ul>
				</div>
				<div class="content-block-title">收货人</div>
				<div class="list-block media-list">
					<ul>
					@foreach ($data['cust']['lkmans'] as $item)
						@if ($item['lk_type'] == 1)
						<li>
							<a href="#" class="item-content">
								<div class="item-inner">
									<div class="item-title-row">
										<div class="item-title">{{$item['linkman_name']}}</div>
										<div class="item-after">{{$item['lk_phone']}}</div>
									</div>
									<div class="item-subtitle">{{$item['province']}} {{$item['city']}} {{$item['area']}}<br/>{{$item['lk_address']}}</div>
								</div>
							</a>
						</li>
						@endif
					@endforeach
					</ul>
				</div>
				<div class="content-block-title">发票信息</div>
				<div class="list-block">
					<ul>
						<li class="item-content">
							<div class="item-inner">
								<div class="item-title">税号</div>
								<div class="item-after">{{$data['cust']['ticket_info']['taxno']}}</div>
							</div>
						</li>
						<li class="item-content">
							<div class="item-inner">
								<div class="item-title">开户银行</div>
								<div class="item-after">{{$data['cust']['ticket_info']['bank']}}</div>
							</div>
						</li>
						<li class="item-content">
							<div class="item-inner">
								<div class="item-title">银行账号</div>
								<div class="item-after">{{$data['cust']['ticket_info']['bankno']}}</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
	<script type="text/javascript" src="//g.alicdn.com/msui/sm/0.6.2/js/sm-city-picker.min.js" charset="utf-8"></script>
</body>
</html>