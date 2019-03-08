<?php
return [
	'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', 'wx3b08c8e3c237b6b0'),
	'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', 'f8209e8fa58f882f2f3e770cabc09928'),
	'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', 'KuG4XGCdscXDXPR4'),
	'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', 'uSAAnWMYYWCWy3OG06hnso8L5VTmMIUk'),
	'oauth' => [
		'scopes'   => array_map('trim', explode(',', env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_SCOPES', 'snsapi_userinfo'))),
		'callback' => env('WECHAT_OFFICIAL_ACCOUNT_OAUTH_CALLBACK', '/oauth_callback'),
	],
];
