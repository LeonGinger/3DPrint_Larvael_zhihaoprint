<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <title>我的订单</title>
	    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
	    <link rel="shortcut icon" href="/favicon.ico">
	    <meta name="apple-mobile-web-app-capable" content="yes">
	    <meta name="apple-mobile-web-app-status-bar-style" content="black">
	    <link rel="stylesheet" href="/css/sm.min.css">
	    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
	    <style>

.flow {
	width: 100%;
	
	height: 50px;
	position: relative;
	overflow-x: scroll;
}

.flowList {
	float: left;
	height: 1px;
	border:1px solid #ccc;
	background: #ccc;
	width:60px;
	position: relative;
}
.flowListBox {
	position: absolute;
	width:420px;
	left: 0;
	top: 10px;
	margin:0 auto;
}

.flowListBox .flowList em {
	margin-top: -7px;
	display: inline-block;
	width: 14px;
	height: 14px;
	-moz-border-radius: 100%;
	-webkit-border-radius: 100%;
	border-radius: 100%;
	text-align: center; 
	line-height: 14px;
	font-style: normal;
	font-weight: bold;
	vertical-align: middle;
	color: #fff;
	box-shadow: 0 0 2px 2px rgba(255, 255, 255, 0.56);
	cursor:pointer;
	position: absolute; left: calc(50% - 7px); background-color: rgb(204, 204, 204); border: 0px none rgb(0, 0, 0);
}

.flowListBox .flowList strong {
	display: inline-block;
	height: 20px;
	line-height: 20px;
	font-weight: 400;
	cursor:pointer;
	position:absolute;left:0;top:10px;
	width:100%;text-align:center;
	font-size:.5rem;
}

.flowListBox .for-cur{
	border: 1px solid #ff9500; background-color: #ff9500;
}
.flowListBox .for-cur em
{
	background-color: #ff9500; border: 0px;
}
.flowListBox .for-goon{
	border: 1px solid #0894ec; background-color: #0894ec;
}
.flowListBox .for-goon em
{
	background-color: #0894ec; border: 0px none rgb(0, 0, 0);
}
.flowListBox .for-goon strong
{
	color:#0894ec
}
.item-pr10 span{padding-right:10px;display:inline-block}
.cancle:before{content:' ';display:block;position:absolute;top:0;left:0;width:100%;height:100%;z-index:20;background:#000;cursor:not-allowed;text-align:center;opacity: 0.1;}
.cancle:after{content:'已取消';color:#fff;font-size:40px;position:absolute;top:calc(50% - 30px);left:0;width:100%;z-index:21;text-align:center;}

.tenname{font-size: .6rem;}
.picker-item{font-size: .8rem}
	    </style>
	</head>
	<body>
	<div class="page-group">
		<div class="page page-current" id='routers-index'>
		<header class="bar bar-nav">
			<form class="searchbar row">
			<div class="search-input col-50" style="margin-left:0">
			<label class="icon icon-search" for="search"></label>
			<input type="tel" id="search" @if(!empty($data['search']['orde'])) value="{{$data['search']['orde']}}"@endif name="orde" placeholder="输入订单编号...">
			</div>
			<input type="text" @if(!empty($data['search']['stat'])) value="{{$data['search']['stat']}}"@endif name="stat" class="button button-light col-33" style="height: 1.4rem;top:0;background:#fff;border:0" placeholder="所有订单" id='picker'/>
			<input class="button button-fill button-primary col-20" style="top:0;height: 1.4rem;width:auto" type="submit" value="搜索" />
			<input id="tenanid" type="hidden" name="teid" value="0" />
			</form>
		</header>
		<div class="bar bar-header-secondary"><a href="#" class="changeten button button-light">@if(empty($data['tenanlist']['text']))切换商家 @else {{$data['tenanlist']['text']}} @endif</a></div>
		@include('wechat.common.navbar')
		<div class="content">
			@if(empty($data['val']['data']))
				<h6 style="text-align:center">无订单信息,请修改搜索参数。</h6>
			@endif
			@foreach ($data['val']['data'] as $item)
			<div class="card @if($item['status'] == 8 )cancle @endif" id="{{ $item['no'] }}">
				<div class="card-header">
					<div>
						<div class="tenname color-gray">{{ $item['tenname'] }}</div>
						<div>订单编号 : {{ $item['no'] }}</div>
					</div>
					<a href="#" class="button button-light odbut" style="border:1px solid #0894ec" data-status="{{$item['status']}}" data-quoturl="{{ $item['quoturl'] }}" data-contract="{{ $item['contract'] }}" data-proplan="{{ $item['proplan'] }}" data-logistics="{{ $item['logistics'] }}" data-evaluate="{{ $item['evaluate'] }}" data-aftersale="{{ $item['aftersale'] }}" data-reload="{{ $item['reload'] }}"><span class="icon icon-app" style="color:#0894ec;"></span></a>
				</div>
				<div class="card-content">
					<div class="list-block media-list">
						<ul>
							@foreach ($item['parts'] as $part)
							<li>
								<a href="
								@if ($item['status'] == 0)
									javascript:$.toast('订单审核中');
								@elseif ($item['status'] == 1)
									{{ $item['quoturl'] }}
								@elseif ($item['status'] > 1 && $item['status']<= 4)
									{{ $item['proplan'] }}
								@else
									{{ $item['logistics'] }}
								@endif
								" class="item-link item-content external">
								<div class="item-media">
									<img src="{{ $part['diagram'] }}" data-title="{{ $part['name'] }}" class="pbimg"  width="44">
								</div>
								<div class="item-inner">
									<div class="item-title-row">
										<div class="item-title">{{ $part['name'] }}</div>
									</div>
									<div class="item-title-row color-gray" style="font-size:.7rem">
										<div class="item-pr10"><span>{{ $part['matname'] }}</span><span>{{ $part['modname'] }}</span><span>{{ $part['surname'] }}</span><span>{{ $part['equname'] }}</span></div>
										<span>{{ $part['product_num'] }}件</span>
									</div>
								</div>
								</a>
							</li>
							@endforeach
						</ul>
					</div>
				</div>
				<div class="card-footer">
					<div>
						{{ date('Y-m-d',strtotime($item['created_at'])) }}
					</div>
			    	<div>
						<strong style="font-size:1rem;color:red">￥{{ $item['total'] }}</strong>
					</div>
			    </div>
				@if($item['status'] != 8 )
				<div class="flow">
						<div class="flowListBox" data-status="{{$item['status']}}">
							<div class="flowList" >
								<em>1</em><strong>等待审核</strong>
							</div>
							<div class="flowList">
								<em>2</em><strong>确认报价单</strong>
							</div>
							<div class="flowList">
								<em>3</em><strong>准备生产</strong>
							</div>
							<div class="flowList">
								<em>4</em><strong>生产中</strong>
							</div>
							<div class="flowList">
								<em>5</em><strong>生产完成</strong>
							</div>
							<div class="flowList">
								<em>6</em><strong>已发货</strong>
							</div>
							<div class="flowList">
								<em>7</em><strong>完成</strong>
							</div>
						</div>
					</div>
					@endif
			</div>
			@endforeach
		<div class="content-block">
    <div class="row">
		<div class="col-50">
		@if(empty($data['val']['prev_page_url']))
			<a href="javascript:;" class="button disabled">上一页</a>
		@else
			<a href="{{$data['val']['prev_page_url']}}" class="button">上一页</a>
		@endif
		</div>
		<div class="col-50">
		@if(empty($data['val']['next_page_url']))
			<a href="javascript:;" class="button disabled">下一页</a>
		@else
			<a href="{{$data['val']['next_page_url']}}" class="button">下一页</a>
		@endif
		</div>
    </div>
	</div>
		</div>
		<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
		<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" charset="utf-8">
    wx.config(@json($data['jssdk']));
	$('#dialogsFalse1').click(function(){
		wx.closeWindow();
	});
	if(localStorage.first == null || localStorage.first == ''){
		$('body').append('<div id="slideshow" style="height:100%;width:100%;position:fixed;z-index:111111;background:url(/uploads/images/navg.jpg);background-size:cover;transition: opacity 1s,z-index 1s;" onclick="javascript:$(this).css({opacity:0,z_index:-1});"></div>')
		localStorage.first = 'false';
	}
	function pkok(){
	}
	var pbimg = new Array();
	var groups = new Array();
$(function(){
	$('.pbimg').each(function(i){
		$(this).click(function(){
			pbimg[i] = $.photoBrowser({photos : [{ url:$(this).attr('src'),caption: $(this).data('title')}],type: 'popup'});
			pbimg[i].open();
			
		});
	});
	$('.changeten').picker({
		toolbarTemplate: '<header class="bar bar-nav">\
		<button class="button button-link pull-right close-picker">确定</button>\
		<h1 class="title">请选择要切换的公司</h1>\
		</header>',
		cols: [
			{
			textAlign: 'center',
			values: @json($data['tenanlist']['values']),
			displayValues: @json($data['tenanlist']['displayValues'])
			}
		],
		onClose: function(picker, values){
			$('#tenanid').val(picker.value[0]);
			$(".searchbar").submit();
		}
	});
	$('.odbut').each(function(i){
		$(this).click(function(){
			var quoturl = $(this).data('quoturl');
			var contract = $(this).data('contract');
			var proplan = $(this).data('proplan');
			var logistics = $(this).data('logistics');
			var evaluate = $(this).data('evaluate');
			var status = $(this).data('status');
			var aftersale = $(this).data('aftersale');
			var reload = $(this).data('reload');

			var buttons1 = [
			{
			  text: '请选择',
			  label: true
			},
			{
			  text: '查看报价单',
			  onClick: function() {
				  if(status>=1)
					$.router.load(quoturl);
				else
					$.toast("订单审核中，暂无报价单");
			  }
			},
			{
			  text: '查看合同',
			  onClick: function() {
				  if(status>=2)
						window.location.href=contract;
				else if(status=1)
					$.toast("请先确认报价单");
				else
					$.toast("订单审核中，暂无合同");
			  }
			}
			,
			{
			  text: '查看生产计划',
			  onClick: function() {
				  if(status>=3)
					$.router.load(proplan);
				else
					$.toast("订单未开始生产");
			  }
			}
			,
			{
			  text: '查看物流信息',
			  onClick: function() {
				  if(status>=5)
					$.router.load(logistics);
				else
					$.toast("订单未发货");
			  }
			},
			{
			  text: '订单评价',
			  onClick: function() {
				  if(status>=6)
					$.router.load(evaluate);
				else
					$.toast("订单完成后才能评价");
			  }
			},
			{
			  text: '申请售后',
			  onClick: function() {
				$.router.load(aftersale);
			  }
			},
			{
			  text: '再次下单',
			  onClick: function() {
				$.router.load(reload);
			  }
			}
		  ];
		  var buttons2 = [
			{
			  text: '取消',
			  bg: 'danger'
			}
		  ];
		  groups[i] = [buttons1, buttons2];
		  $.actions(groups[i]);
			
		});
	});
	$('.flowListBox').each(function(i){
		var inx = $(this).data('status');
		var item = $(this).children();
		item.each(function(j){
			if(j < inx)
				$(this).addClass('for-goon');
			else if(j == inx)
				$(this).addClass('for-cur');
			else
				return false;
		});
		if(inx > 3)
			$(this).parent().scrollLeft(420 - $(this).parent().width());
			
	});
	$("#picker").picker({
		toolbarTemplate: '<header class="bar bar-nav">\
		<button class="button button-link pull-right close-picker pickersearch" onClick="pkok()">确定</button>\
		<h1 class="title">选择订单状态</h1>\
		</header>',
		cols: [
			{
			textAlign: 'center',
			values: ['所有订单', '等待审核', '确认报价单', '准备生产', '生产中', '生产完成', '已发货', '完成'],
			}
		],
		onClose:function(){
			$(".searchbar").submit();
		}
	});
});
		</script>
		<script>
		
document.body.addEventListener('touchmove' , function(e){
    e.preventDefault();
})
</script>
		</div>
	</div>
	</body>
</html>
