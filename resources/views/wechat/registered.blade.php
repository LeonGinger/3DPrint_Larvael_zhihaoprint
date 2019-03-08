<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
		@include('wechat.common.title')
		<link rel="stylesheet" type="text/css" href="/we-ui/css/weui.css"/>
		<link rel="stylesheet" type="text/css" href="/we-ui/css/register.css"/>
		<script type="text/javascript" src="/we-ui/js/zepto.min.js"></script>
		<style>
		</style>
	</head>
	<body>
		 
		     <div class="js_dialog" id="iosDialog1">
		     <div class="weui-mask"></div>
		     <div class="weui-dialog">
		     	<div class="weui-dialog__bd">您已经提交过入驻申请，请耐心等待审核。</div>
		     	<div class="weui-dialog__ft">
		     		<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary" id='dialogsFalse1'>知道了</a>
		     	</div>
		     </div>
		     </div>
		 <script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
		 <script type="text/javascript" charset="utf-8">
    wx.config(@json($jssdk));
	$('#dialogsFalse1').click(function(){
		wx.closeWindow();
	});
</script>
	</body>
</html>