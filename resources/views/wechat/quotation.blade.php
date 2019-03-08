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
	    <link rel="stylesheet" href="/css/sm.min.css">
	    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
	    <style>
	    /*	.content{background-image: linear-gradient(to top, #66b7f9,#1c82d4);}*/
	    	.content h4{margin: 1rem 0 0 1rem; display: flex; justify-content: center; }
	        .partFont{font-size: 0.7rem;}
	        .add,.reduce{display: inline-block; width: 1.3rem; height: 1.3rem;border: 1px solid #d6d6d6; border-radius: 50%; text-align: center;line-height: 1.3rem;}
			.num{padding: 0 !important;display:inline-block !important;text-align:left !important;overflow:visible !important}
	        .popup h1{text-align: center; font-size: 1.2rem;}
	        .end-bottom{ margin-top: 1rem; padding-bottom: 1rem; width: 100%; text-align: right; padding-right:1.2rem ;}
	        .end-bottom a{width: 3rem; display: inline-block;height: 1.35rem; line-height: 1.35rem;}
	        .poup{position: relative;}
	        .partFont div{margin-top:0.2rem ;}
	        .button1{display: inline-block; width: 5rem;height:2rem;line-height: 2rem; background-color: #1C82D4;color: #ffffff;border-radius: 0.8rem;}
	    	.xx_card{background: #fff;padding: 15px;}
	        .xx_card .xx_card_title{padding-bottom: 10px;}
	        .xx_card .xx_card_item{padding-bottom:10px;color:#000;font-size: 14px;display: flex;	}
	        .xx_card .xx_card_item > div{flex-shrink:1}
	        .xx_card .xx_card_item .xx_card_item_title{width: 90px;color:#888;margin-right:10px;display: inline-block;flex-shrink:0}
	        .xx_card .xx_card_item .xx_card_item_sub_title{width:60px;display: inline-block;color:#999}
	        .list-border .item-inner:after{background-color: #ffffff;}
	        .list-border .list-div{width: 90%; display: flex; justify-content:space-between;margin: 0 auto; border-bottom:1px solid #e7e7e7 ;}
	    </style>
	</head>
	<body>
		<form action="" method="post" id="postfrom">
		<div class="content">
			<h4 style='text-align: center;'>供需方信息区域</h4>
			<div class="card">
			    <div class="card-content">
			<div class="xx_card">
				
				<div class="xx_card_body">
					<div class="xx_card_item"><span class="xx_card_item_title">报价单号</span>{{$data['val']['no']}}</div>
					<div class="xx_card_item"><span class="xx_card_item_title">报价日期</span>{{$data['val']['created_at']}}</div>
					<div class="xx_card_item"><span class="xx_card_item_title">购买方</span>{{$data['val']['cusname']}}</div>
					<div class="xx_card_item"><span class="xx_card_item_title">联系人</span>
						<div>
					@foreach ($data['val']['linkmans'] as $item)
						<div class="xx_card_subitem">{{ $item['linkman_name'] }}&nbsp;&nbsp;{{ $item['lk_phone'] }}</div>
					@endforeach
						</div>
					</div>
					<div class="xx_card_item"><span class="xx_card_item_title">邮寄地址</span>
						<div>
						<div>{{$data['val']['postaddr']['linkman_name']}}&nbsp;&nbsp;{{$data['val']['postaddr']['lk_phone']}}</div>
						<div>{{$data['val']['postaddr']['province']}}{{$data['val']['postaddr']['city']}}{{$data['val']['postaddr']['area']}}{{$data['val']['postaddr']['lk_address']}}</div>
						</div>
					</div>
					@if(!empty($data['val']['ticket_info']['taxno']) && !empty($data['val']['ticket_info']['bank']) && !empty($data['val']['ticket_info']['bankno']) && !empty($data['val']['invoaddr']))
					<div class="xx_card_item"><span class="xx_card_item_title">发票信息</span>
						<div>
						<div><span class="xx_card_item_sub_title">税号</span>{{$data['val']['ticket_info']['taxno']}}</div>
						<div><span class="xx_card_item_sub_title">开户行</span>{{$data['val']['ticket_info']['bank']}}</div>
						<div><span class="xx_card_item_sub_title">账号</span>{{$data['val']['ticket_info']['bankno']}}</div>
						</div>
					</div>
					<div class="xx_card_item"><span class="xx_card_item_title">发票邮寄地址</span>
						<div>
							<div>{{$data['val']['invoaddr']['linkman_name']}}&nbsp;&nbsp;{{$data['val']['invoaddr']['lk_phone']}}</div>
							<div>{{$data['val']['invoaddr']['province']}}{{$data['val']['invoaddr']['city']}}{{$data['val']['invoaddr']['area']}}{{$data['val']['invoaddr']['lk_address']}}</div>
						</div>
					</div>
					@endif
					<div class="xx_card_item"><span class="xx_card_item_title">服务商</span>{{$data['val']['tanname']}}</div>
					<div class="xx_card_item"><span class="xx_card_item_title">销售工程师</span>{{$data['val']['saler']['name']}}&nbsp;&nbsp;{{$data['val']['saler']['phone']}}</div>
					<div class="xx_card_item"><span class="xx_card_item_title">项目工程师</span>{{$data['val']['pojer']['name']}}&nbsp;&nbsp;{{$data['val']['pojer']['phone']}}</div>
					<div class="xx_card_item"><span class="xx_card_item_title">项目经理</span>{{$data['val']['manger']['name']}}&nbsp;&nbsp;{{$data['val']['manger']['phone']}}</div>
				</div>
				<input type="hidden" name="val" value="{{$data['val']['val']}}"/>
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
			              	<img style='width:120px' class="pbimg" data-title="{{ $item['name'] }}" src="{{ $item['diagram'] }}">
			            </div>
			            <div class="item-inner partFont">
			                <div>{{ $item['matname'] }} {{ $item['modname'] }} {{ $item['surname'] }} {{ $item['equname'] }}</div>
			                <div>
								
								<div class="" style="white-space: nowrap;float:right">
									<span class="reduce">-</span>
									<input type="number" @if ($data['val']['status'] == 0) disabled="disabled" @endif onchange="this.value=this.value.replace(/\D/g,'')" class="num" style="width: {{strlen($item['product_num'])*10}}px" size="{{strlen($item['product_num'])+1}}" name="parts[{{$item['id']}}][product_num]" data-int="{{ $item['product_num'] }}" value="{{ $item['product_num'] }}">
									<span class="add">+</span>
								</div>
								<span style="line-height: 2.6rem;" class="pice" data-pice="{{ $item['couprice'] }}">￥{{ $item['couprice'] }}</span>
							</div>
			            </div>
			          </li>
			        </ul>
			      </div>
				  <div class="card-content-inner">
					<?php $volsi = json_decode($item['volume_size'],true) ?>
					长:{{ $volsi['xx'] }}mm&nbsp;&nbsp;宽:{{ $volsi['zz'] }}mm&nbsp;&nbsp;高:{{ $volsi['yy'] }}mm<br/>
					体积:@if(strlen($volsi['volume'])>3)
						{{ $volsi['volume'] / 1000 }}cm3
					@else
						{{ $volsi['volume'] }}mm3
					@endif
				  </div>
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
			  	 <div class="list-block media-list list-border">
			      <ul>
			        <li>
			          <label class="label-checkbox item-content">
			          	<div class="list-div">
			          		<div class="item-inner">
			              	<div id='part'><span style="text-decoration:underline">零件检验质量标准</span></div>
				            </div>
				            <input type="checkbox" name="checkbox"  id="partValue" >
				            <div class="item-media">同意&nbsp;<i class="icon icon-form-checkbox" ></i></div>
			          	</div>
			            
			          </label>
			        </li>
			        <li>
			          <label class="label-checkbox item-content">
			            <div class="list-div" style="border: 0;">
				            <div class="item-inner">
				              <div id="jiaofu"><span style="text-decoration:underline">货物交付及结算区域</span></div>
				            </div>
				            <input type="checkbox" name="checkbox" id="jiaofuValue" >
				            <div class="item-media">同意&nbsp;<i class="icon icon-form-checkbox" ></i></div>
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
			 <span style="margin-top: 0.6rem;margin-left: 0.5rem">费用：￥<span id="total">{{$data['val']['total']}}</span><input type="hidden" id="subtotal" name="total" value="{{$data['val']['total']}}"/></span>
			 <input type="submit" class="button button-fill" onclick="javascript:return false;" style="width: auto;border-radius: 0;display:none;line-height: 2.5rem;height: 2.5rem;top: 0;" id="submit" value="确认修改"/>
			 @if ($data['val']['status'] == 1)<a href="javascript:;" class="button button-fill confirm-title-ok" id="okbutn" style="width: 5rem; height: 2.5rem; line-height: 2.5rem;border-radius: 0;top: 0; ">确认报价单 </a>@endif
			 @if ($data['val']['status'] == 0)<a href="javascript:;" class="button button-fill button-light" style="height: 2.5rem; line-height: 2.5rem;border-radius: 0;top: 0; ">制作方正在审核</a>@endif
		</nav>
		</form>
		<div class="popup popup-about poup">
		  <div class="content-block">
		    <h1>零件检验质量标准</h1>
		    <p><?php echo htmlspecialchars_decode(nl2br($data['val']['qs'])); ?></p>
		    <p class="end-bottom" style="text-align: center;"> <a href="#" class="close-popup button button-fill" >同意</a></p>
		  </div>
		</div>
		
		<div class="popup popup-server poup">
		  <div class="content-block">
		    <h1>货物交付及结算区域</h1>
		    <p id="text"><?php echo htmlspecialchars_decode(nl2br($data['val']['qt'])); ?></p>
		   <p class="end-bottom" style="text-align: center;"><a href="#" class="close-popup button button-fill">同意</a></p>
		  </div>
		</div>
		<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
	@if ($data['val']['status'] == 1)
		<script>
			
function getUrl(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return decodeURI(r[2]); return null;
}
				$(function(){
		$('input[type=number]').on('change',function(){
			var tnum = String($(this).val());
			if(tnum.length < 1){
				$(this).val(1);
				$(this).css('width','10px');
			}
			else{
				$(this).css('width',tnum.length * 10 + 'px');
			}
			var fr = Number($('#fr').html());
			var sf = Number($('#sf').html());
			
			var picx = 0;
			var okb = true;
			$('.parts').each(function(i){
				var num=Number($('.parts .num').eq(i).val());
				picx = picx + num * Number($('.parts .pice').eq(i).data('pice'));
				var intnum = $('.parts .num').eq(i).data('int');
				if(num != Number(intnum))
					if(okb)okb=false;
			});
			if(okb){
				$('#submit').hide();$('#okbutn').show();
			}else{
				$('#submit').show();$('#okbutn').hide();
			}
			picx = picx + fr + sf;
			$('#total').html(picx.toFixed(2));
			$('#subtotal').val(picx.toFixed(2));
		});
				  $('#submit').click(function(){
					  $.confirm('确认修改报价单吗？修改后制作方将重新审核后会再次邀请您重新确认报价。', '报价单修改', function () {
$.showPreloader('请稍等。。');
var subdata = $('form').serializeArray();
$.ajax({
  type: 'POST',
  url: "{{url('/api/wechat/quotation/edit')}}",
  data: subdata,
  dataType: 'json',
  success: function(data){
	  $.hidePreloader();
	  $.alert('报价单已经修改，等待制作方审核。', '报价单修改成功',function () {
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
						var picx = tot + Number($('.parts .pice').eq(i).data('pice'));
						$('#total').html(picx.toFixed(2));
						$('#subtotal').val(picx.toFixed(2));
					});
					$('.parts .reduce').eq(i).click(function(){
						var num=Number($('.parts .num').eq(i).val());
						if(num>1){
							$('.parts .num').eq(i).val(num-1);
							var tot = Number($('#total').html());
							var picx =tot - Number($('.parts .pice').eq(i).data('pice'));
							$('#total').html(picx.toFixed(2));
							$('#subtotal').val(picx.toFixed(2));
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
			  	
			  	$(document).on('click','.confirm-title-ok', function () {
			  		let partValue=document.getElementById('partValue');
			  	let jiaofuValue=document.getElementById('jiaofuValue');
			  		if(partValue.checked&&jiaofuValue.checked){
			  			 $.confirm('确认', '报价单', function () {
$.showPreloader('请稍等。。');
$.ajax({
  type: 'POST',
  url: "{{url('/api/wechat/quotation/confirm')}}",
  data: { val: "{{$data['val']['val']}}" },
  dataType: 'json',
  success: function(data){
	  $.hidePreloader();
	  $.alert('报价单已经确认，我们将尽快安排生产。', '报价单已确认',function () {
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
})

				      });
			  		}else{
			  			 $.alert('请勾选零件质量标准和货物交付及结算区域');
			  		}
				     
				});
			  	$.config = {
				   autoInit: true
				}
			  	
			  })
		</script>
	@endif
	<script>
	var pbimg = new Array();
	$(function(){
		$('.pbimg').each(function(i){
			$(this).click(function(){
				pbimg[i] = $.photoBrowser({photos : [{ url:$(this).attr('src'),caption: $(this).data('title')}],type: 'popup'});
				pbimg[i].open();
			});
		});
				$(".poup").click(function(){
			  		$.closeModal();
			  	});
			  	let part=document.getElementById('part');
			  	let jiaofu=document.getElementById('jiaofu');
			  	
			  	part.onclick=function(){
			  		$.popup('.popup-about');
			  	}
			  	jiaofu.onclick=function(){
			  		$.popup('.popup-server');
			  	}
	});
	</script>
	<script>
		$('input[type=number]').on('blur',function(){
			setTimeout(function(){window.scrollTo(0, 0)},100);
		});//解决微信6.7.4以上输入框关闭后页面空缺的问题
		$('textarea').on('blur',function(){setTimeout(function(){window.scrollTo(0, 0)},100)});//解决微信6.7.4以上输入框关闭后页面空缺的问题
	</script>
	</body>
</html>
