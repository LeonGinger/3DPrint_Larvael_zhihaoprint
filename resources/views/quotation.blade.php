<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <title>报价单</title>
	    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
	    <link rel="shortcut icon" href="/favicon.ico">
	    <meta name="apple-mobile-web-app-capable" content="yes">
	    <meta name="apple-mobile-web-app-status-bar-style" content="black">
	    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">
	    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
	    <style>
	    /*	.content{background-image: linear-gradient(to top, #66b7f9,#1c82d4);}*/
	    	.content h4{margin: 1rem 0 0 1rem; display: flex; justify-content: center; }
	        .partFont{font-size: 0.7rem;}
	        .add,.reduce{display: inline-block; width: 1.3rem; height: 1.3rem;border: 1px solid #d6d6d6; border-radius: 50%; text-align: center;line-height: 1.3rem;}
			.num{padding: 0 !important;display:inline-block !important;width:auto !important;text-align:left !important;overflow:visible !important}
	        .popup h1{text-align: center; font-size: 1.2rem;}
	        .end-bottom{ margin-top: 1rem; padding-bottom: 1rem; width: 100%; text-align: right; padding-right:1.2rem ;}
	        .end-bottom a{width: 3rem; display: inline-block;height: 1.35rem; line-height: 1.35rem;}
	        .poup{position: relative;}
	        .partFont div{margin-top:0.2rem ;}
	        .button1{display: inline-block; width: 5rem;height:2rem;line-height: 2rem; background-color: #1C82D4;color: #ffffff;border-radius: 0.8rem;}
	    </style>
	</head>
	<body>
		<header class="bar bar-nav">
		  <h1 class="title">报价单</h1>
		</header>
		<form action="" method="post" id="postfrom">
		<div class="content">
			<h4 style='text-align: center;'>供需方信息区域</h4>
			<div class="card">
				<input type="hidden" name="val" value="{{$data['val']['val']}}"/>
			    <div class="card-content">
			      <div class="card-content-inner">
			      	<div>报价单号 : {{$data['val']['no']}}</div>
					<div>报价日期 : {{$data['val']['created_at']}}</div>
					<div>购买方 : {{$data['val']['cusname']}}</div>
					<div>联系人 :
					@foreach ($data['val']['linkmans'] as $item)
						<p class="weui-media-box__desc">{{ $item['linkman_name'] }}&nbsp;&nbsp;{{ $item['lk_phone'] }}</p>
					@endforeach
					</div>
					<div>
						邮寄地址：
						<p class="weui-media-box__desc">{{$data['val']['postaddr']['linkman_name']}}&nbsp;&nbsp;{{$data['val']['postaddr']['lk_phone']}}</p>
					<p class="weui-media-box__desc">{{$data['val']['postaddr']['province']}}{{$data['val']['postaddr']['city']}}{{$data['val']['postaddr']['area']}}{{$data['val']['postaddr']['lk_address']}}</p>
					</div>
					<div>
						<h5>开票信息</h5>
						<p class="weui-media-box__desc">税号：{{$data['val']['ticket_info']['taxno']}}</p>
						<p class="weui-media-box__desc">开户行：{{$data['val']['ticket_info']['bank']}}</p>
						<p class="weui-media-box__desc">账号：{{$data['val']['ticket_info']['bankno']}}</p>
					</div>
					<div>
						<h5>发票邮寄地址</h5>
						<p class="weui-media-box__desc">{{$data['val']['invoaddr']['linkman_name']}}&nbsp;&nbsp;{{$data['val']['invoaddr']['lk_phone']}}</p>
						<p class="weui-media-box__desc">{{$data['val']['invoaddr']['province']}}{{$data['val']['invoaddr']['city']}}{{$data['val']['invoaddr']['area']}}{{$data['val']['invoaddr']['lk_address']}}</p>
					</div>
					<div>
						<p>销售工程师 :{{$data['val']['saler']['name']}}&nbsp;&nbsp;{{$data['val']['saler']['phone']}}</p>
						<p>项目工程师 : {{$data['val']['pojer']['name']}}&nbsp;&nbsp;{{$data['val']['pojer']['phone']}}</p>
						<p>项目经理 : {{$data['val']['manger']['name']}}&nbsp;&nbsp;{{$data['val']['manger']['phone']}}</p>
					</div>
			      </div>
			    </div>
			</div>
			<h4 style='text-align: center;'>零件清单区域</h4>
			@foreach ($data['val']['parts'] as $item)
			<div class="card parts">
			    <div class="card-header">{{ $item['name'] }}</div>
			    <div class="card-content">
			      <div class="list-block media-list">
			        <ul>
			          <li class="item-content">
			            <div class="item-media">
			              	<img style='width:120px' src="{{ $item['diagram'] }}">
			            </div>
			            <div class="item-inner partFont">
			                <div>{{ $item['matname'] }} {{ $item['modname'] }} {{ $item['surname'] }} {{ $item['equname'] }}</div>
			                <div>
								
								<div class="" style="white-space: nowrap;float:right">
									<span class="add">+</span>
									<input type="text" class="num"  size="{{strlen($item['product_num'])+1}}" name="parts[{{$item['id']}}][product_num]" data-int="{{ $item['product_num'] }}" value="{{ $item['product_num'] }}">
									<span class="reduce">-</span>
								</div>
								<span style="line-height: 2.6rem;" class="pice" data-pice="{{ $item['couprice'] }}">￥{{ $item['couprice'] }}</span>
							</div>
			            </div>
			          </li>
			        </ul>
			      </div>
				  <div class="card-content-inner">{{ $item['volume_size'] }}</div>
			    </div>
			    <div class="card-footer">
              <textarea style="width: 100%;border-color:#f0f0f0" name="parts[{{$item['id']}}][remark]" class="remark" placeholder='备注'>{{ $item['remark'] }}</textarea>
			      	
			    </div>
			</div>	
					@endforeach
			<div class="card">
			    <div class="card-content">
			      <div class="card-content-inner">
			      	<div>运费 : ￥<span id="fr">{{$data['val']['taxation']}}</span></div>
			      	<div>税费 :￥<span id="sf">{{$data['val']['freight']}}</span></div>
			      </div>
			    </div>
		  	<!--</div>
		  	<div class="card">-->
			  	 <div class="list-block media-list">
			      <ul>
			        <li>
			          <label class="label-checkbox item-content" id='part'>
			            <input type="checkbox" name="checkbox"  id="partValue" >
			            <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
			            <div class="item-inner">
			              <div>零件检验质量标准</div>
			            </div>
			          </label>
			        </li>
			        <li>
			          <label class="label-checkbox item-content" id="jiaofu">
			            <input type="checkbox" name="checkbox" id="jiaofuValue" >
			            <div class="item-media"><i class="icon icon-form-checkbox"></i></div>
			            <div class="item-inner">
			              <div>货物交付及结算区域</div>
			            </div>
			          </label>
			        </li>
			      </ul>
			    </div>
			     <div class="card-content">
			      <div class="card-content-inner">
			      	<div>备注:</div>
			      	<div class="">
		              <textarea style="width: 100%; border: 0;" name="remark" class="remark" placeholder='备注'>{{$data['val']['remark']}}</textarea>
		            </div>
			      </div>
			    </div>   
		    </div>
		  	<div style='height: 2.5rem;'></div>
		</div>
		
		<nav class="bar bar-tab" style="display: flex;justify-content: space-between; padding-right: 0.5rem;  z-index: 4;" >
			 <span style="margin-top: 0.6rem;margin-left: 0.5rem">费用：￥<span id="total">{{$data['val']['total']}}</span></span>
			 <input type="submit" class="button button-fill" onclick="javascript:return false;" style="width: auto;border-radius: 0;display:none;line-height: 2.5rem;height: 2.5rem;top: 0;" id="submit" value="修改报价单"/>
			 @if ($data['val']['status'] < 2)<a href="javascript:;" class="button button-fill confirm-title-ok" id="okbutn" style="width: 5rem; height: 2.5rem; line-height: 2.5rem;border-radius: 0;top: 0; ">确认报价单 </a>@endif
		</nav>
		
		<div class="popup popup-about poup">
		  <div class="content-block">
		    <h1>零件检验质量标准</h1>
		    <p><?php echo htmlspecialchars_decode(nl2br($data['val']['qs'])); ?></p>
		    <p class="end-bottom" style="text-align: center;"> <a href="#" class="close-popup button button-fill" >关闭</a></p>
		  </div>
		</div>
		
		<div class="popup popup-server poup">
		  <div class="content-block">
		    <h1>货物交付及结算区域</h1>
		    <p id="text"><?php echo htmlspecialchars_decode(nl2br($data['val']['qt'])); ?></p>
		   <p class="end-bottom" style="text-align: center;"><a href="#" class="close-popup button button-fill">关闭</a></p>
		  </div>
		</div>
		</form>
		<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
	@if ($data['val']['status'] < 2)
		<script>
function getUrl(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return decodeURI(r[2]); return null;
}
			  $(function(){
				  $('#submit').click(function(){
					  $.confirm('确认修改报价单吗？修改后制作方将重新审核后会再次邀请您重新确认报价。', '报价单修改', function () {
$.showPreloader('请稍等。。');
var subdata = $('form').serializeArray();
$.ajax({
  type: 'POST',
  url: 'https://yp-dev.one2fit.cn/api/wechat/quotation/edit',
  // data to be added to query string:
  data: subdata,
  // type of data we are expecting in return:
  dataType: 'json',
  success: function(data){
	  window.location.href="/customer/orderlist";
	 
  },
  error: function(xhr, type, error){
	  console.log(xhr);
   alert('发生错误，请刷新重试。')
  }
})

				      });
				  });
				  $('.parts').each(function(i){
					$('.parts .add').eq(i).click(function(){
						var num=Number($('.parts .num').eq(i).val());
						$('.parts .num').eq(i).val(num+1);
						var intnum = $('.parts .num').eq(i).data('int');
						num=Number($('.parts .num').eq(i).val());
						if(Number(num) == Number(intnum)){
							$('#submit').hide();$('#okbutn').show();
						}else{
							$('#submit').show();$('#okbutn').hide();
						}
						var tot = Number($('#total').html());
						var picx = Number($('.parts .pice').eq(i).data('pice'));
						$('#total').html(tot+picx);
					});
					$('.parts .reduce').eq(i).click(function(){
						var num=Number($('.parts .num').eq(i).val()) 
						if(num>1){
							$('.parts .num').eq(i).val(num-1);
							var tot = Number($('#total').html());
							var picx = Number($('.parts .pice').eq(i).data('pice'));
							$('#total').html(tot+picx);
						}else{
							$('.parts .num').eq(i).val(1)
						}
						var intnum = $('.parts .num').eq(i).data('int');
						num=Number($('.parts .num').eq(i).val());
						if(Number(num) == Number(intnum)){
							$('#submit').hide();$('#okbutn').show();
						}else{
							$('#submit').show();$('#okbutn').hide();
						}
					})
				});
				$('.remark').each(function(i){
					var obj = $(this);
					var objval = $(this).val();
					$(this).on('input',function(){
						if($(this).val() == objval){
							$('#submit').hide();$('#okbutn').show();
						}else{
							$('#submit').show();$('#okbutn').hide();
						}
					});
				});
			  	$(".poup").click(function(){
			  		$.closeModal();
			  	});
			  	let part=document.getElementById('part');
			  	let jiaofu=document.getElementById('jiaofu');
			  	let partValue=document.getElementById('partValue');
			  	let jiaofuValue=document.getElementById('jiaofuValue');
			  	part.onclick=function(){
			  		$.popup('.popup-about');
			  	}
			  	jiaofu.onclick=function(){
			  		$.popup('.popup-server');
			  	}
			  	$(document).on('click','.confirm-title-ok', function () {
				      $.confirm('确认', '报价单', function () {
$.showPreloader('请稍等。。');
$.ajax({
  type: 'POST',
  url: 'https://yp-dev.one2fit.cn/api/wechat/quotation/confirm',
  // data to be added to query string:
  data: { val: getUrl('val') },
  // type of data we are expecting in return:
  dataType: 'json',
  success: function(data){
	  
	  window.location.href="/customer/orderlist";
  },
  error: function(xhr, type, error){
	  
   alert('发生错误，请刷新重试。')
  }
})

				      });
				});
			  	$.config = {
				   autoInit: true
				}
			  	
			  })
		</script>
	@endif
	</body>
</html>
