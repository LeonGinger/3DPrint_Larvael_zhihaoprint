<!DOCTYPE html><html>
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
				<h1 class="title">帮助</h1>
			</header>
			@include('wechat.common.navbar')
			<div class="content">
				<div class="content-block-title">操作说明</div>
				<div class="list-block media-list inset">
					<ul>
						<li>
							<a href="#" class="item-link item-content">
								<div class="item-inner">
									<div class="item-subtitle">微信端界面说明文档...</div>
								</div>
							</a>
						</li>
						<li>
							<a href="#" class="item-link item-content">
								<div class="item-inner">
									<div class="item-subtitle">订单状态解释...</div>
								</div>
							</a>
						</li>
						<li>
							<a href="#" class="item-link item-content">
								<div class="item-inner">
									<div class="item-subtitle">系统工作流程介绍...</div>
								</div>
							</a>
						</li>
					</ul>
				</div>
				<div class="content-block-title">常见问题</div>
				<div class="list-block media-list inset">
					<ul>
						<li>
							<a href="#" class="item-link item-content">
								<div class="item-inner">
									<div class="item-subtitle">微信收不到新订单通知</div>
								</div>
							</a>
						</li>
						<li>
							<a href="#" class="item-link item-content">
								<div class="item-inner">
									<div class="item-subtitle">订单状态改变后无法收到微信消息</div>
								</div>
							</a>
						</li>
						<li>
							<a href="#" class="item-link item-content">
								<div class="item-inner">
									<div class="item-subtitle">如何解除微信绑定</div>
								</div>
							</a>
						</li>
					</ul>
				</div>
				<div class="content-block-title">建议和反馈</div>
				<div class="list-block inset">
					<ul>
						<li>
							<div class="item-content">
								<div class="item-media"><i class="icon icon-edit"></i></div>
								<div class="item-inner">
									<div class="item-input">
										<select>
											<option>发展建议</option>
											<option>问题反馈</option>
										</select>
									</div>
								</div>
							</div>
						</li>
						<li>
							<div class="item-content">
								<div class="item-inner">
									<div class="item-input">
										<textarea placeholder="请输入您的建议或反馈。。。"></textarea>
									</div>
								</div>
							</div>
						</li>
						<li>
							<a href="#" id="cencelbind" class="button button-fill button-big">确认提交</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
</body>
</html>