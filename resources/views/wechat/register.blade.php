
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
			<h1>???????????????</h1>
		  <div class="list-block">
		    <div >????????????</div>
		    <div id="show1">??????????????????</div>
		    <div><input type="tel" name="phone" placeholder="????????????????????????" id="phone" /></div>
		    <div id="show"><input type="number" name="code" placeholder="????????????????????????"  id="yzm"/><a id="gecode" >?????????????????????</a></div>
		    <div class="alert-text-title"><a href="javascript:void(0);" class="alert-text-title">??????</a></div>
		    <div>???????????????????????????<a href="javascript:;" id="yinsi">??????????????????</a></div>
			<input type="hidden" name="vk" value='' id="vk" />
			<input type="hidden" name="vp" value='' id="vp" />
			<input type="hidden" name='vo' value="{{ $data['user']['id'] }}"/>
		  </div>
		</div>
	</form>
<div class="popup popup-yinsi">
  <div class="content-block close-popup">
    <h4>??????????????????</h4>
    <p>??????????????????????????????</p>
    <p><a href="#" class="button close-popup">??????</a></p>
  </div>
</div>
		<div class="nav"><div>???????????????????????????</div></div>
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
	o.innerHTML="?????????????????????";
	wait = 60;
	} else {
	o.innerHTML="????????????(" + wait + ")";
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
//           			$.alert('???????????????????????????');
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
									$.alert('?????????????????????????????????');
								}else{
									$.hideIndicator();
									$.alert(ero.message, '????????????');
								}
							}
						});
					}
				});
             	 $(document).on('click','.alert-text-title', function () {
             	 	if(phone.value&&yzm.value){// && $('#vk').val() != ''
							var modal = $.modal({
								title: '????????????',
								text: '???????????????????????????????????????',
								afterText:  '<div class="list-block">'+
								'<input type="password" placeholder="Password" id="passord">'+
								'</div>',
								buttons: [
									{
										text: '??????',
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
$.showPreloader('?????????????????????????????????');
$.ajax({
  type: 'POST',
  url: '{{url("/api/wechat/register")}}',
  data:JSON.stringify(formObject),
  contentType: 'application/json',
  success: function(dataw){
	  $.hidePreloader();
    $.alert('?????????????????????????????????????????????????????????????????????');
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
					etx = etx + "?????????????????????" + "<br/>";
					break;
				case 'vo':
					etx = etx + "??????????????????" + "<br/>";
					break;
				case 'phone':
					etx = etx + "????????????????????????????????????" + "<br/>";
					break;
				case 'code':
					etx = etx + "????????????????????????" + "<br/>";
					break;
				default:
					etx = etx + "???????????????????????????????????????" + "<br/>";
			}
		})
	 }else{
		 etx = ero.message;
	 }
	 $.alert(etx, '?????????');
		 
  }
});
											}else{
												$.toast("?????????????????????????????????+????????????");
												document.getElementById('passord').focus();
											}
										}
									},
								]
							});
             	 	}else{
             	 		$.alert('???????????????');
             	 	}
			    });
			    var winHeight = $(window).height();
				$(window).resize(function(){  //?????????????????????
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
