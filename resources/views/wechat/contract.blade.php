<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>送货单</title>
		<style>
		@font-face {
                font-family: 'msyh';
                font-style: normal;
                font-weight: normal;
                src: url(https://yp-dev.one2fit.cn/fonts/STXINWEI.TTF) format('truetype');
            }
			div,p,ul,li{margin: 0;padding: 0;font-family:'msyh'}
			.main{font-size: 16px;}
			.main .title{text-align: center; font-size: 28px;}
			.main .qs{height:160px}
			.main .zzf{margin-top: 10px;}
			.main .partsTitle{display: flex; justify-content: center;margin-bottom: 10px;}
			.main ul{}
			.main ul li{list-style: none; border: 1px solid #d6d6d6;height:140px;width: 45%; margin-bottom: 10px; padding:5px ;overflow:hidden;display:inline-block;}
		    .main ul li img{width:35%;float:left}
			.main ul li .dies{font-size: 12px;}
			.main ul li>p{14px;}
			.main .main-footer div{margin-top:10px ; }
			.main .main-footer div .radio{width: 18px; height: 18px; vertical-align: text-bottom;}
			.main .main-footer div input[type='text']{
				height: 18px;
				line-height: 18px;
			}
			input[type="radio" i]{
				background-color: #D6D6D6;
				color:#D6D6D6;
			}
		</style>
	</head>
	<body>
		<div class="main">
			<div class="title">合同</div>
			<p>报价单号 : {{ $data['no'] }}</p>
			<p>报价日期 : {{ $data['created_at'] }}</p>
			<p>{{ $data['cusname'] }}</p>
			<p>收货方 : </p>
			@foreach ($data['linkmans'] as $user)
			<p>　　　　{{$user['linkman_name']}},{{$user['province']}} {{$user['city']}} {{$user['area']}} {{$user['lk_address']}},{{$user['lk_phone']}}</p>
			@endforeach
			<div class="zzf">制作方</div>
			<p>销售工程师 : {{ $data['saler']['name'] }},{{ $data['saler']['phone'] }};</p>
			<p>项目工程师 : {{ $data['pojer']['name'] }},{{ $data['pojer']['phone'] }};</p>
			<p>项目经理 : {{ $data['manger']['name'] }},{{ $data['manger']['phone'] }};</p>
			<div class="partsTitle">零件清单区域</div>
			<div>
			<ul>
				@foreach ($data['parts'] as $part)
				<li>
					<p>{{$part['name']}}</p>
					<div>
						<img src="{{$part['diagram']}}" alt="">
						<div class="dies">
							<p>{{$part['matname']}}&nbsp;&nbsp;{{$part['modname']}}&nbsp;&nbsp;{{$part['surname']}}&nbsp;&nbsp;{{$part['equname']}}</p>
						    <p>订单号 : {{$part['orderno']}}</p>
							<p>数量 : {{$part['yscount']}}</p>
						</div>
					</div>
					<p  class="dies">{{$part['volume_size']}}</p>
				</li>
				@endforeach
			</ul>
			</div>
			<div>
				<h4>零件检验质量标准</h4>
				 <?php echo htmlspecialchars_decode(nl2br($data['qs'])); ?>
			</div>
			<div>
				<h4>货物交付以及结算区域</h4>
				 <?php echo htmlspecialchars_decode(nl2br($data['qt'])); ?>
			</div>
			
		</div>
	</body>
</html>