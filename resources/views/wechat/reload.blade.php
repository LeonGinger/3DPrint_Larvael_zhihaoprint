<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>再次下单</title>
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
			<h1 class="title">再次下单</h1>
		</header>
		@include('wechat.common.navbar')
		<div class="content">
			<div class="content-block-title">订单信息</div>
			<form action="" method="post" id="postfrom">
			<div class="list-block media-list">
				<ul>
					<li class="list-group-title item-content" style="padding:0">
							<div class="item-media" style="padding-left: .75rem;color:#000">批量设置下单数量：</div>
							<div class="item-inner" style="margin-left:0">
								<div class="item-title-row">
								<div style="display:inline-block;position:relative;padding-right:1.5rem">  
									<div style="position:absolute;right:5px;top:calc(50% - .5rem);cursor:pointer;display:none;" class="input_clear">
										<a class="button button-light button-round" style="height: 1rem;line-height: 1rem !important;width: 1rem;padding: 0;text-align: center;color:#ccc">✕</a>
									</div>  
									<input type="number" placeholder="点击输入" name="keyword" id="keyword">
								</div>
								<a href="javascript:;" class="button plshezhi" style="margin-top:8px">设置</a>
								</div>
							</div>
					</li>
					@foreach ($data['val']['parts'] as $part)
					<li class="item-content">
						<div class="item-media"><img src="{{ $part['diagram'] }}" data-title="{{ $part['name'] }}" class="pbimg" style="width: 2.2rem;"></div>
						<div class="item-inner">
							<div class="item-title-row">
								<div class="item-title">{{ $part['name'] }}</div>
							</div>
							<div class="item-title-row">
								<div class="item-title" style="font-size: .85rem;width:11rem;font-weight: normal;">下单数量(件)</div>
								<div class="item-input" style="display:inline-block;position:relative;padding-right:1.5rem">  
									<div style="position:absolute;right:5px;top:calc(50% - .5rem);cursor:pointer;display:none;" class="input_clear">
										<a class="button button-light button-round" style="height: 1rem;line-height: 1rem !important;width: 1rem;padding: 0;text-align: center;color:#ccc">✕</a>
									</div>  
									<input type="number" placeholder="点击输入" name="pid[{{ $part['id'] }}]">
								</div>
							</div>
						</div>
					</li>
					@endforeach
				</ul>
			</div>
			<input type="hidden" name="val" value="{{$data['oid']}}" >
			</form>
			<div class="content-block">
				<a href="javascript:;" id="submit" class="button button-big button-fill">确定下单</a>
			</div>
			
		</div>
		<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
		<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
		<script>
		$("input").focus(function(){  
		$(this).parent().children(".input_clear").show();  
		});  
		$("input").blur(function(){  
		if($(this).val()=='')  
		{  
		$(this).parent().children(".input_clear").hide();  
		}  
		});  
		$(".input_clear").click(function(){  
		$(this).parent().find('input').val('');  
		$(this).hide();  
		}); 
		</script>
		<script type="text/javascript" charset="utf-8">
			var pbimg = new Array();
			wx.config(@json($data['jssdk']));
			$('#dialogsFalse1').click(function(){
				wx.closeWindow();
			});
			$(function(){
				$('.plshezhi').click(function(){
					$('input[type=number]').val($('#keyword').val());
				});
				$('.pbimg').each(function(i){
					$(this).click(function(){
						pbimg[i] = $.photoBrowser({photos : [{ url:$(this).attr('src'),caption: $(this).data('title')}],type: 'popup'});
						pbimg[i].open();
					});
				});
				$('#submit').click(function(){
					@if($data['val']['status'] >= 1 )
							$.showPreloader('请稍等。。');
							var subdata = $('#postfrom').serializeArray();
							$.ajax({
								type: 'POST',
								url: '{{url('/api/wechat/order/reload')}}',
								data: subdata,
								dataType: 'json',
								success: function(data){
									$.hidePreloader();
									$.alert(data.message, function () {
										window.location.href="/customer/orderlist";
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
					@else
						$.alert('此订单还未生效。');
					@endif
				});
			});
		</script>
		<script>
		$('input[type=number]').on('blur',function(){setTimeout(function(){window.scrollTo(0, 0)},100);});
		</script>
	</body>
</html>