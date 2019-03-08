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
		<link rel="stylesheet" type="text/css" href="{{url('evaluate')}}/iconfont.css">
		<style>
			label.label-checkbox input[type=radio]:checked+.item-media i.icon-evaluate:before{content: "\e7f0";}
			label.label-checkbox input[type=radio]:checked+.item-media i.icon-bad:before{content: "\e614";}
			label.label-checkbox input[type=radio]:checked~label.label-checkbox{color:red}
			label.label-checkbox input[type=radio]:checked+.item-media i.icon,label.label-checkbox input[type=radio]:checked~div.item-inner{color:red}
			label.label-checkbox input[type=checkbox]:checked+.item-media i.icon-roundcheck{color:#1296db}
			.iconfont{font-size:1.1rem !important;line-height: 1.1rem;}
			.item-title{font-size:.7rem !important}
			.evaluate{display:flex;justify-content:space-between}
			.star i{color:#ea9518}
			.star-txt{padding-left:10px;font-size:.7rem;vertical-align: text-bottom;}
			.bar,.content{position: fixed !important;}
			.item-pr10 span{padding-right:10px;display:inline-block}
		</style>
	</head>
	<body>
		<header class="bar bar-nav">
			<a class="button button-link button-nav pull-left back" href="/customer/orderlist">
			<span class="icon icon-left"></span>返回</a>
			<h1 class="title">订单评价</h1>
		</header>
		<div class="content">
			<div class="list-block media-list">
				<ul>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title">订单编号：{{$data['val']['no']}}</div>
						</div>
					</div>
				</li>
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
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title-row">
								<div class="item-title color-gray" style="font-size:.7rem;font-weight:normal !important;">{{date('Y-m-d',strtotime($data['val']['crdate']))}}</div>
								<div class="item-after" style="font-size:.7rem">
									@if(!empty($data['val']['eva']))
										已评价
									@else
										未评价
									@endif
								</div>
							</div>
						</div>
					</div>
				</li>
				</ul>
			</div>
			@if(!empty($data['val']['eva']))
			<div class="content-block-title">客户对本单的评价</div>
			<div class="list-block media-list">
				<ul class="evaluate">
					<li>
						<div class="item-content">
							@switch($data['val']['eva']['pingjia'])
								@case(1)
							<div class="item-media"><i class="iconfont icon icon-badfill"></i></div>
							<div class="item-inner">
								<div class="item-subtitle">差评</div>
							</div>
								@break
								@case(2)
							<div class="item-media"><i class="iconfont icon icon-badfill"></i></div>
							<div class="item-inner">
								<div class="item-subtitle">中评</div>
							</div>
								@break
								@case(3)
							<div class="item-media"><i class="iconfont icon icon-evaluate_fill"></i></div>
							<div class="item-inner">
								<div class="item-subtitle">好评</div>
							</div>
							@endswitch
						</div>
					</li>
				</ul>
				<ul>
					<li>
						<div class="item-content">
							<div class="item-inner">
								<div class="item-input">
									<textarea placeholder="请填写您对本次服务的感受吧，对我们帮助很大哦~~~~" disabled name="content">{{$data['val']['eva']['content']}}</textarea>
								</div>
							</div>
						</div>
					</li>
				</ul>
				@if($data['val']['eva']['anonymous'])
				<ul>
					<li>
						<div class="item-content">
							<div class="item-inner">
								<div class="item-title-row">
									<div class="item-title"></div>
									<div class="item-subtitle color-gray" style="font-size:.7rem">匿名</div>
								</div>
							</div>
						</div>
					</li>
				</ul>
				@endif
			</div>
			<div class="content-block-title">客户对其他人员的评价</div>
			<div class="list-block">
				<ul>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title label">销售工程师</div>
							<div class="item-input">
								<input type="hidden" name="saler_star" class="hdstar">
								<span class="star">
								@for ($i = 0; $i < 5; $i++)
									@if($i <= $data['val']['eva']['saler_star'])
										<i class="iconfont icon-favorfill"></i>
									@else
										<i class="iconfont icon-favor"></i>
									@endif
								@endfor
								</span>
								<span class="star-txt color-gray"></span>
							</div>
						</div>
					</div>
				</li>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title label">项目工程师</div>
							<div class="item-input">
								<input type="hidden" name="pojer_star" class="hdstar">
								<span class="star">
								@for ($i = 0; $i < 5; $i++)
									@if($i <= $data['val']['eva']['pojer_star'])
										<i class="iconfont icon-favorfill"></i>
									@else
										<i class="iconfont icon-favor"></i>
									@endif
								@endfor
								</span>
								<span class="star-txt color-gray"></span>
							</div>
						</div>
					</div>
				</li>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title label">项目经理</div>
							<div class="item-input">
								<input type="hidden" name="manger_star" class="hdstar">
								<span class="star">
								@for ($i = 0; $i < 5; $i++)
									@if($i <= $data['val']['eva']['manger_star'])
										<i class="iconfont icon-favorfill"></i>
									@else
										<i class="iconfont icon-favor"></i>
									@endif
								@endfor
								</span>
								<span class="star-txt color-gray"></span>
							</div>
						</div>
					</div>
				</li>
				</ul>
			</div>
			@else
			<form action="" method="post" id="postfrom">
			<div class="content-block-title">您对本单的评价</div>
			<div class="list-block media-list">
				<ul class="evaluate">
					<li>
						<label class="label-checkbox item-content">
							<input type="radio" checked="checked" name="pingjia" value="3">
							<div class="item-media"><i class="iconfont icon icon-evaluate"></i></div>
							<div class="item-inner">
								<div class="item-subtitle">好评</div>
							</div>
						</label>
					</li>
					<li>
						<label class="label-checkbox item-content">
							<input type="radio" name="pingjia" value="2">
							<div class="item-media"><i class="iconfont icon icon-bad"></i></div>
							<div class="item-inner">
								<div class="item-subtitle">中评</div>
							</div>
						</label>
					</li>
					<li>
						<label class="label-checkbox item-content">
							<input type="radio" name="pingjia" value="1">
							<div class="item-media"><i class="iconfont icon icon-bad"></i></div>
							<div class="item-inner">
								<div class="item-subtitle">差评</div>
							</div>
						</label>
					</li>
				</ul>
				<ul>
					<li>
						<div class="item-content">
							<div class="item-inner">
								<div class="item-input">
									<textarea placeholder="请填写您对本次服务的感受吧，对我们帮助很大哦~~~~" name="content"></textarea>
								</div>
							</div>
						</div>
					</li>
				</ul>
				<ul>
					<li>
						<div class="item-title-row">
							<div class="item-title"></div>
							<label class="label-checkbox item-content color-gray">
								<input type="checkbox" name="anonymous" value="1">
								<div class="item-media"><i class="iconfont icon icon-roundcheck"></i></div>
								<div class="item-inner">
									<div class="item-subtitle">匿名</div>
								</div>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="content-block-title">对其他人员的评价</div>
			<div class="list-block">
				<ul>
				<!-- Text inputs -->
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title label">销售工程师</div>
							<div class="item-input allstar">
								<input type="hidden" name="saler_star" class="hdstar">
								<span class="star">
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								</span>
								<span class="star-txt color-gray"></span>
							</div>
						</div>
					</div>
				</li>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title label">项目工程师</div>
							<div class="item-input allstar">
								<input type="hidden" name="pojer_star" class="hdstar">
								<span class="star">
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								</span>
								<span class="star-txt color-gray"></span>
							</div>
						</div>
					</div>
				</li>
				<li>
					<div class="item-content">
						<div class="item-inner">
							<div class="item-title label">项目经理</div>
							<div class="item-input allstar">
								<input type="hidden" name="manger_star" class="hdstar">
								<span class="star">
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								<i class="iconfont icon-favor"></i>
								</span>
								<span class="star-txt color-gray"></span>
							</div>
						</div>
					</div>
				</li>
				</ul>
			</div>
			<div class="content-block">
				<input type="hidden" name="oid" value="{{$data['oid']}}">
				<a href="javascript:;" id="submit" class="button button-big button-fill">提交评价</a>
			</div>
			</form>
			@endif
		</div>
		
		<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
		<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
			var arrBtxt=["非常差","差","一般","好","非常好"];
			var pbimg = new Array();
			wx.config(@json($data['jssdk']));
			$('#dialogsFalse1').click(function(){
				wx.closeWindow();
			});
			$(function(){
				$('.pbimg').each(function(i){
					$(this).click(function(){
						pbimg[i] = $.photoBrowser({photos : [{ url:$(this).attr('src'),caption: $(this).data('title')}],type: 'popup'});
						pbimg[i].open();
					});
				});
				$('.allstar').each(function(index){
					$(this).find('i').each(function(ix){
						$(this).click(function(){
							$('.star').eq(index).children('i').removeClass('icon-favorfill').addClass('icon-favor');
							$('.hdstar').eq(index).val(ix);
							$('.star-txt').eq(index).html(arrBtxt[ix]);
							for(var i=0;i<=ix;i++){
								$('.star').eq(index).children('i').eq(i).removeClass('icon-favor').addClass('icon-favorfill');
							}
						});
					});
				});
				$('#submit').click(function(){
					var subdata = $('form').serializeArray();
					console.log(subdata);
					var isok = true;
					$.each(subdata,function(index, item){
						if(item['value'] == ''){
							switch(item['name']){
								case "content":
									$.toast("请输入评价内容");break;
								case "saler_star":
									$.toast("请点评一下销售工程师吧");break;
								case "pojer_star":
									$.toast("请点评一下项目工程师吧");break;
								case "manger_star":
									$.toast("请点评一下项目经理吧");break;
							}
							isok = false;
							return false;
						}
					});
					if(!isok)return false;
					$.confirm('评价提交后即生效', '确定提交评价吗？', function () {
						$.showPreloader('请稍等。。');
						$.ajax({
							type: 'POST',
							url: 'https://yp-dev.one2fit.cn/api/wechat/order/evaluate',
							data: subdata,
							dataType: 'json',
							success: function(data){
								$.hidePreloader();
								$.alert('感谢您的评价~', '评价成功', function () {
									window.location.href='/customer/orderlist';
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
				  });
			});
		</script>
		<script>
		$('textarea').on('blur',function(){setTimeout(function(){window.scrollTo(0, 0)},100)});//解决微信6.7.4以上输入框关闭后页面空缺的问题
		</script>
	</body>
</html>