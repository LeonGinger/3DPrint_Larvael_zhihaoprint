<!DOCTYPE html><html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
		<title>出错了</title>
		<link rel="stylesheet" href="https://cdn.bootcss.com/weui/1.1.3/style/weui.min.css">
		<link rel="stylesheet" href="https://cdn.bootcss.com/jquery-weui/1.2.1/css/jquery-weui.min.css">
		<script src="https://cdn.bootcss.com/jquery/1.11.0/jquery.min.js"></script>
		<script src="https://cdn.bootcss.com/jquery-weui/1.2.1/js/jquery-weui.min.js"></script>
		<script src="https://cdn.bootcss.com/jquery-weui/1.2.1/js/swiper.min.js"></script>
		</head>
		<body style="background:#f3f3f3">
		<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
    wx.config({"debug":false,"beta":false,"jsApiList":["closeWindow"],"appId":"wx3b08c8e3c237b6b0","nonceStr":"EE8o6OFsTg","timestamp":1544525083,"url":"https:\/\/yp-dev.one2fit.cn\/customer\/orderlist","signature":"e38d970503d5c277058433294342fe111903feba"});
		</script>
		<script language="JavaScript">
		$.alert("{{ $exception->getMessage() }}", "出错了",function() {wx.closeWindow();});
		</script>
</body>
</html>