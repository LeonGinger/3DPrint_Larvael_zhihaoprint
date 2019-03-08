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
                src: url(https://yp-dev.one2fit.cn/fonts/MSYH.TTF) format('truetype');
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
			<div class="title">送货单</div>
			<p>报价单号 : {{ $data['no'] }}</p>
			<p>报价日期 : {{ $data['created_at'] }}</p>
			<div class="qs">
				<p>订单二维码 :</p>
				<p><img width="120px" src="https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=3809074820,3835012484&fm=26&gp=0.jpg" alt="" /></p>
			</div>
			<p>{{ $data['cusname'] }}</p>
			<p>收货方 : </p>
			@foreach ($data['lkmans'] as $user)
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
			<div class="main-footer">
				<form action="">
					<div>
					@if ($data['dismod'] == 0)
						<label>快递</label>
					@elseif ($data['dismod'] == 1)
						<label>自送</label>
					@else
						<label>自提</label>
					@endif
						
					</div>
					@if (!empty($data['express_name']))
					<div>
						<label>快递公司 :{{ $data['express_name'] }}</label>
					</div>
					<div>
						<label>快递单号 :{{ $data['express_no'] }}</label>
					</div>
					@endif
				</form>
			</div>
		</div>
	</body>
</html>