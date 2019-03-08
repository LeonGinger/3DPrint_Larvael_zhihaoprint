<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    return view('welcome');
    echo 'ำฏฦีฯ๎ฤฟ-สืาณ';
});

Route::get('STLUpload','Home\STLUploadController@ShowStlMod');
Route::get('wechat/register','Home\WechatController@Register');
Route::get('wechat/register/vercode','Home\WechatController@Vercode');
Route::get('oauth_callback','Home\WechatController@OauthCallback');
Route::get('quotation','Home\QuotationController@GetQuotation');
Route::get('customer/orderlist','Home\OrderListController@GetOrders');
Route::get('wechat/order/procuplan','Home\ProductPlanController@PartPlanList');
Route::get('wechat/order/logistics','Home\LogisticsController@Logistics');
Route::get('wechat/order/contract','Home\ContractController@GetContract');
Route::get('wechat/order/evaluate','Home\EvaluateController@Index');
Route::get('wechat/order/aftersale','Home\AftersaleController@Index');
Route::get('wechat/order/reload','Home\ReloadController@Index');

Route::get('wechat/admin/evaluate','Home\EvaluateController@Admin');

Route::get('customer/message','Home\OrderListController@MessageList');
Route::get('customer/setting','Home\OrderListController@Setting');
Route::get('customer/help','Home\OrderListController@Hepl');
Route::any('wechat/service','Home\WechatController@Service');
Route::any('wechat/wechatpay','Home\WechatController@WechatPay');

Route::get('wechat/fastorder','Home\ContractController@GetFastorder');