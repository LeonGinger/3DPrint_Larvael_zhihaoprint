<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width,initial-scale=1.0"/>
		<title>报价单</title>
		<link rel="stylesheet" type="text/css" href="/we-ui/css/weui.css"/>
		<link rel="stylesheet" type="text/css" href="/we-ui/css/quotation.css"/>
		<script type="text/javascript" src="/we-ui/js/zepto.min.js"></script>
		
	</head>
	<body>
<div class="page panel js_show">
    <div class="page__hd" style="text-align:center;padding:10px 0">
        <h1 class="page__title">报价单</h1>
    </div>
    <div class="page__bd">
		<h4 style="text-align:center">供需方信息区域</h4>
        <div class="weui-panel weui-panel_access">
            <div class="weui-panel__hd"><strong style="color:#000">报价单号：</strong>{{$data['val']['no']}}</div>
			<div class="weui-panel__hd"><strong style="color:#000">报价日期：</strong>{{$data['val']['created_at']}}</div>
			<div class="weui-panel__hd"><strong style="color:#000">购买方：</strong>{{$data['val']['cusname']}}</div>
			<div class="weui-panel__bd">
				<div class="weui-media-box weui-media-box_text">
					<h5>联系人</h5>
					@foreach ($data['val']['linkmans'] as $item)
						<p class="weui-media-box__desc">{{ $item['linkman_name'] }}&nbsp;&nbsp;{{ $item['lk_phone'] }}</p>
					@endforeach
				</div>
				<div class="weui-media-box weui-media-box_text">
					<h5>零件邮寄地址</h5>
					<p class="weui-media-box__desc">{{$data['val']['postaddr']['linkman_name']}}&nbsp;&nbsp;{{$data['val']['postaddr']['lk_phone']}}</p>
					<p class="weui-media-box__desc">{{$data['val']['postaddr']['province']}}{{$data['val']['postaddr']['city']}}{{$data['val']['postaddr']['area']}}{{$data['val']['postaddr']['lk_address']}}</p>
				</div>
				<div class="weui-media-box weui-media-box_text">
					<h5>开票信息</h5>
					<p class="weui-media-box__desc">税号：{{$data['val']['ticket_info']['taxno']}}</p>
					<p class="weui-media-box__desc">开户行：{{$data['val']['ticket_info']['bank']}}</p>
					<p class="weui-media-box__desc">账号：{{$data['val']['ticket_info']['bankno']}}</p>
				</div>
				<div class="weui-media-box weui-media-box_text">
					<h5>发票邮寄地址</h5>
					<p class="weui-media-box__desc">{{$data['val']['invoaddr']['linkman_name']}}&nbsp;&nbsp;{{$data['val']['invoaddr']['lk_phone']}}</p>
					<p class="weui-media-box__desc">{{$data['val']['invoaddr']['province']}}{{$data['val']['invoaddr']['city']}}{{$data['val']['invoaddr']['area']}}{{$data['val']['invoaddr']['lk_address']}}</p>
				</div>
			</div>
				<div>
					
				</div>
        </div>
        <div class="weui-panel weui-panel_access">
			<div class="weui-panel__hd">制作方信息</div>
			<div class="weui-media-box weui-media-box_text">
				<h5>销售工程师</h5>
				<p class="weui-media-box__desc">{{$data['val']['saler']['name']}}&nbsp;&nbsp;{{$data['val']['saler']['phone']}}</p>
			</div>
			<div class="weui-media-box weui-media-box_text">
				<h5>项目工程师</h5>
				<p class="weui-media-box__desc">{{$data['val']['pojer']['name']}}&nbsp;&nbsp;{{$data['val']['pojer']['phone']}}</p>
			</div>
			<div class="weui-media-box weui-media-box_text">
				<h5>项目经理</h5>
				<p class="weui-media-box__desc">{{$data['val']['manger']['name']}}&nbsp;&nbsp;{{$data['val']['manger']['phone']}}</p>
			</div>
		</div>
    </div>
    <div class="page__ft">
        
    </div>
</div>
		<div class="main">
			<div class="title">零件清单区域</div>
			<div class="parts">
				<ul>
					@foreach ($data['val']['parts'] as $item)
					<li>
						<span>
						<img src="{{ $item['diagram'] }}" alt="">
						</span>
						<span>
							<p>{{ $item['name'] }}</p>
							<p> {{ $item['matname'] }} {{ $item['modname'] }} {{ $item['surname'] }} {{ $item['equname'] }}</p>
							<p>￥<span class="price">{{ $item['couprice'] }}</span><span class="right"><span class='add'>+</span>&nbsp;<span class="num">{{ $item['product_num'] }}</span>&nbsp;<span class="reduce">-</span></span> </p>
						</span>
						<p>{{ $item['volume_size'] }}</p>
					</li>
					@endforeach
				</ul>
				<div class="fr">运费 : ￥<span id="fr">{{$data['val']['taxation']}}</span></div>
				<div class='sf'>税费 : ￥<span id="sf">{{$data['val']['freight']}}</span></div>
				<div class="weui-cells weui-cells_checkbox">
					<label class="weui-cell weui-check__label " for="s11" id="partCheck">
						<div class="weui-cell__hd">
							<input type="checkbox" class="weui-check" name="checkbox1" id="s11" />
							<i class="weui-icon-checked"></i>
						</div>
						<div class="weui-cell__bd">
							<p>零件检验质量标准</p>
						</div>
					</label>
					<label class="weui-cell weui-check__label" for="s12" id="goodsCheck">
						<div class="weui-cell__hd">
							<input type="checkbox" name="checkbox1" class="weui-check" id="s12"/>
							<i class="weui-icon-checked"></i>
						</div>
						<div class="weui-cell__bd">
							<p>货物交付以及结算区域</p>
						</div>
					</label>
				</div>
				<div class="bz">备注</div>
				<div class="bzs">
					<textarea class="weui-textarea bzf" placeholder="请输入备注" rows="3"></textarea>
				</div>
						
				
			</div>
		</div>
		<div class="bottom" style="border-top:1px solid #f0f0f0">
				<div>
					<p>合计 :￥<span id="totalPrice">{{$data['val']['total']}}</span></p> @if ($data['val']['status'] < 2)<span id="totalButton" class="button">确认报价单</span>@endif
				</div>
			</div>
			
	<div class="js_dialog" id="iosDialog1" style="display: none;">
		<div class="weui-mask"></div>
		<div class="weui-dialog">
			<div class="weui-dialog__hd"><strong class="weui-dialog__title">零件检验质量标准</strong></div>
			<div class="weui-dialog__bd">{{$data['val']['qs']}}</div>
			<div class="weui-dialog__ft">
				<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default iosDialog1False" >取消</a>
				<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary iosDialog1False" >确定</a>
			</div>
		</div>
	</div>	
		<div class="js_dialog" id="iosDialog2" style="display: none;">
			<div class="weui-mask"></div>
			<div class="weui-dialog">
				<div class="weui-dialog__hd"><strong class="weui-dialog__title">货物交付以及结算区域</strong></div>
				<div class="weui-dialog__bd">{{$data['val']['qt']}}</div>
				<div class="weui-dialog__ft">
					<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default iosDialog2False" >取消</a>
					<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary iosDialog2False" >确定</a>
				</div>
			</div>
		</div>
	<div id="toast" style="opacity: 0; display: none;">
        <div class="weui-mask_transparent"></div>
        <div class="weui-toast">
            <i class="weui-icon-success-no-circle weui-icon_toast"></i>
            <p class="weui-toast__content">已完成</p>
        </div>
    </div>
	<div id="loadingToast" style="opacity: 0; display: none;">
        <div class="weui-mask_transparent"></div>
        <div class="weui-toast">
            <i class="weui-loading weui-icon_toast"></i>
            <p class="weui-toast__content">数据加载中</p>
        </div>
    </div>
<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
    wx.config(@json($data['jssdk']));
	$('#dialogsFalse1').click(function(){
		wx.closeWindow();
	});
</script>
<script>
function getUrl(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return decodeURI(r[2]); return null;
}

			$(function(){
				let $iosDialog1 = $('#iosDialog1');
				let $iosDialog2 = $('#iosDialog2');
				$('#s11').click(function(){
					// 质量检测
					if($('#s11').is(':checked')){
						$iosDialog1.fadeIn(200);
					}
				})
				$('#s12').click(function(){
					// 货物交付
					if($('#s12').is(':checked')){
						$iosDialog2.fadeIn(200);
					}
				})
				$('.iosDialog1False').click(function(){
					$iosDialog1.fadeOut(200);
				})
				$('.iosDialog2False').click(function(){
					$iosDialog2.fadeOut(200);
				})
				$('#totalButton').click(function(){
					var $loadingToast = $('#loadingToast');
	  $loadingToast.fadeIn(100);
					$.ajax({
  type: 'POST',
  url: 'https://yp-dev.one2fit.cn/api/wechat/quotation/confirm',
  // data to be added to query string:
  data: { val: getUrl('val') },
  // type of data we are expecting in return:
  dataType: 'json',
  success: function(data){
	  var $toast = $('#toast');
	  $loadingToast.fadeOut(100);
	  $toast.fadeIn(100);
	  window.location.href="/customer/orderlist";
  },
  error: function(xhr, type, error){
   alert('发生错误，请刷新重试。')
  }
})
				})
				$('.parts li').each(function(i){
					$('.parts li .add').eq(i).click(function(){
						var num=Number($('.parts li .num').eq(i).text()) 
						$('.parts li .num').eq(i).text(num+1)
					})
					$('.parts li .reduce').eq(i).click(function(){
						var num=Number($('.parts li .num').eq(i).text()) 
						if(num>0){
							$('.parts li .num').eq(i).text(num-1)
						}else{
							$('.parts li .num').eq(i).text(0)
						}
						
					})
				})
			})
		</script>
	</body>
</html>