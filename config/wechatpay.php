<?php
return [
	//'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID'),
	'app_id' => 'wx3b08c8e3c237b6b0',
	'mch_id' => env('WECHAT_OFFICIAL_PAY_MCH_ID', '1522489711'),
	'key' => env('WECHAT_OFFICIAL_PAY_KEY', 'QxVKbmoWfdryK7TMn5yk1NCeiS7Trs9u'),
	'notify_url' => env('WECHAT_OFFICIAL_PAY_NOTIFY_URL', env('APP_URL').'/wechat/wechatpay'),
	//'sandbox' => true,
];
