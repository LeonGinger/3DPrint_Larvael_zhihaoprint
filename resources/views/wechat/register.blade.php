
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    @include('wechat.common.title')
	    <meta name="viewport" content="initial-scale=1, maximum-scale=1">
	    <link rel="shortcut icon" href="/favicon.ico">
	    <meta name="apple-mobile-web-app-capable" content="yes">
	    <meta name="apple-mobile-web-app-status-bar-style" content="black">
	    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm.min.css">
	    <link rel="stylesheet" href="//g.alicdn.com/msui/sm/0.6.2/css/sm-extend.min.css">
	    <style>
	    	.content{}
	    	.list-block{width: 92%; margin: 1.5rem auto 0;background-color: #FFFFFF; border-radius:5px ;}
	    	.list-block div:first-child{padding:1rem 0;text-align: center;font-size: 1.2rem;}
	    	.list-block div{width: 90%;margin: 0 auto ;}
	    	.list-block div:nth-child(2){background-color: #fb9760;color: #FFFFFF;text-align: center; font-size: 0.8rem;}
	    	.list-block div:nth-child(3){border: 1px solid #d6d6d6;}
	    	.list-block div:nth-child(4) input{display: inline-block;width: 58%;}
	    	.list-block div:nth-child(4) a{width: 41%; font-size: 0.8rem;}
	    	.list-block div:nth-child(4){border: 1px solid #d6d6d6;margin-top: 1rem;}
	    	.list-block div:nth-child(5){margin-top: 1rem;}
	    	.list-block div:nth-child(5) a{display: block; background-color: #288fd1; color:#ffffff;text-align: center;padding: 0.5rem 0;}
	    	.list-block div:nth-child(6) {margin-top: 1rem; padding-bottom: 1rem;letter-spacing:1px; }
	    	.content h1{margin: 1rem 0 0 0; display: flex; justify-content: center;font-size:1.5rem;}
	        nav>div{text-align:center;line-height: 1.5rem;height: 1.5rem;}
	        #show{display: none;}
	        #show1{display: none;}
	        .bar{height: 1.5rem;background-color: #eee;}
	        .nav{ position: absolute;bottom: 10px; text-align: center;width: 100%; }
			.disabled{color:#ccc;}
			.disabled:active{color:#ccc;}
	    </style>
	</head>
	<body>
	<form id="postForm" action="" method="post" enctype="multipart/form-data">
		<div class="content">
			<h1>小盈云平台</h1>
		  <div class="list-block">
		    <div >商家入驻</div>
		    <div id="show1">手机号不正确</div>
		    <div><input type="tel" name="phone" placeholder="请输入您的手机号" id="phone" /></div>
		    <div id="show"><input type="number" name="code" placeholder="请输入短信验证码"  id="yzm"/><a id="gecode" >获取短信验证码</a></div>
		    <div class="alert-text-title"><a href="javascript:void(0);" class="alert-text-title">注册</a></div>
		    <div>点击注册即表示同意<a href="javascript:;" id="yinsi">用户隐私条款</a></div>
			<input type="hidden" name="vk" value='' id="vk" />
			<input type="hidden" name="vp" value='' id="vp" />
			<input type="hidden" name='vo' value="{{ $data['user']['id'] }}"/>
		  </div>
		</div>
	</form>
<div class="popup popup-yinsi">
  <div class="content-block close-popup">
    <h4>用户隐私条款</h4>
    <p>用户隐私条款详细内容</p>
    <p><a href="#" class="button close-popup">关闭</a></p>
  </div>
</div>
		<div class="nav"><div>技术支持：协成科技</div></div>
		<script type='text/javascript' src='//g.alicdn.com/sj/lib/zepto/zepto.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm.min.js' charset='utf-8'></script>
	    <script type='text/javascript' src='//g.alicdn.com/msui/sm/0.6.2/js/sm-extend.min.js' charset='utf-8'></script>
		<script src="https://res.wx.qq.com/open/js/jweixin-1.4.0.js" type="text/javascript" charset="utf-8"></script>
		 <script type="text/javascript" charset="utf-8">wx.config(@json($data['jssdk']));</script>
		<script>
		var wait=60;
function time(o) {
	if (wait == 0) {
	o.removeAttribute("class");
	o.innerHTML="获取短信验证码";
	wait = 60;
	} else {
	o.innerHTML="重新发送(" + wait + ")";
	wait--; 
	setTimeout(function() {
	time(o)
	},
	1000)
	}
}
			  $(function(){
			  	let phone=document.getElementById('phone');
			  	let yzm=document.getElementById('yzm');
			  	let show=document.getElementById('show');
			  	let show1=document.getElementById('show1');
			    phone.oninput=function(){
             		var myreg=/^[1][3,4,5,7,8][0-9]{9}$/;
             		if (!myreg.test(phone.value)) {
//           			$.alert('请输入正确的手机号');
						show.style.display='none';
						show1.style.display='block';
             		}else{
             			show1.style.display='none';
             			show.style.display='block';
             		}
             	}
				$('#yinsi').click(function(){
					$.popup('.popup-yinsi');
				});
				$('#gecode').click(function(){
					var myreg=/^[1][3,4,5,7,8][0-9]{9}$/;
					if(!$(this).hasClass("disabled") && wait == 60 && myreg.test(phone.value)){
						$.showIndicator();
						$.ajax({
							type: 'POST',
							url: '{{url("/api/verificationCodes")}}',
							data: { phone: phone.value },
							dataType: 'json',
							success: function(data){
								$.hideIndicator();
								$(this).addClass("disabled");
								time(document.getElementById('gecode'));
								$('#vk').val(data['key']);
							},
							error: function(xhr, type, error){
								var ero = $.parseJSON(xhr.responseText);
								if(ero.errors != null){
									$.hideIndicator();
									$.alert('手机号已注册，请更换。');
								}else{
									$.hideIndicator();
									$.alert(ero.message, '出错了！');
								}
							}
						});
					}
				});
             	 $(document).on('click','.alert-text-title', function () {
             	 	if(phone.value&&yzm.value){// && $('#vk').val() != ''
							var modal = $.modal({
								title: '设置密码',
								text: '请设置一个密码用于登录账户',
								afterText:  '<div class="list-block">'+
								'<input type="password" placeholder="Password" id="passord">'+
								'</div>',
								buttons: [
									{
										text: '确定',
										bold: true,
										close: false,
										onClick: function () {
											var myreg=/^(?![^a-zA-Z]+$)(?!\D+$).{6,18}$/;
											if(myreg.test($('#passord').val())){
												$('#vp').val($('#passord').val());
												$.closeModal();
												var formObject = {};
var formArray =$("#postForm").serializeArray();
$.each(formArray,function(i,item){
formObject[item.name] = item.value;
});
console.log(JSON.stringify(formObject));
$.showPreloader('正在注册，请稍后。。。');
$.ajax({
  type: 'POST',
  url: '{{url("/api/wechat/register")}}',
  data:JSON.stringify(formObject),
  contentType: 'application/json',
  success: function(dataw){
	  $.hidePreloader();
    $.alert('恭喜您，入驻成功！您现在可以去登陆使用平台了。');
	wx.closeWindow();
  },
  error: function(xhr){
	  $.hidePreloader();
	  var ero = $.parseJSON(xhr.responseText);
	  var etx = '';
	 if(ero.errors != null){
		 $.each(ero.errors, function(index, item){
			 switch(index){
				case "vk":
					etx = etx + "请先获取验证码" + "<br/>";
					break;
				case 'vo':
					etx = etx + "您已经申请过" + "<br/>";
					break;
				case 'phone':
					etx = etx + "手机号已经注册，请更换。" + "<br/>";
					break;
				case 'code':
					etx = etx + "请输入短信验证码" + "<br/>";
					break;
				default:
					etx = etx + "输入内容有误，请重新输入。" + "<br/>";
			}
		})
	 }else{
		 etx = ero.message;
	 }
	 $.alert(etx, '出错了');
		 
  }
});
											}else{
												$.toast("密码太简单，请输入数字+字母组合");
												document.getElementById('passord').focus();
											}
										}
									},
								]
							});
             	 	}else{
             	 		$.alert('请填写完整');
             	 	}
			    });
			    var winHeight = $(window).height();
				$(window).resize(function(){  //窗口大小改变时
				        var resHeight = $(window).height();
				        if(resHeight!=winHeight) {
				            $(".nav").hide();
				        }else {
				            $(".nav").show();
				        }
				});

			    
			    $.config = {
				    autoInit: true
				}
			  })
		</script>
	</body>
</html>
