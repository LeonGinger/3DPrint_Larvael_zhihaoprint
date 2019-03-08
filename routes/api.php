<?php
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'serializer:array'
], function($api) {
    // 游客可以访问的接口
    $api->post('verificationCodes', 'VerificationCodesController@store'); // 短信验证码
    $api->post('captchas', 'CaptchasController@store'); // 图片验证码
    $api->post('wechat/register', 'WechatController@Register'); // 微信入驻
    $api->post('sendwechatmessage', 'UsersController@testmessage');
	$api->get('tcpdf', 'WechatController@tcpdf');
	$api->post('wechat/order/evaluate', 'WechatController@SendEvaluate');
	$api->post('wechat/order/return', 'WechatController@OrderReturn');
	$api->post('wechat/order/return/cancel', 'WechatController@OrderReturnCancel');
	$api->post('wechat/order/return/rebuid', 'WechatController@OrderReturnRebuid');
	$api->post('wechat/order/return/canrebuid', 'WechatController@OrderReturnCanRebuid');
	$api->post('wechat/order/reload', 'WechatController@OrderReload');
	
	//快速添加报价单
	$api->post('sales/fastorder/add', 'SalesOrderController@FastOrderAdd');
	$api->get('fastorder/material/listall', 'InformationController@FastOrderListMaterialsAll');
	
	//客户确认报价单
	$api->post('wechat/quotation/confirm', 'WechatController@QuotationConfirm');
	//客户确认报价单
	$api->post('wechat/quotation/edit', 'WechatController@QuotationEdit');
	//客户确认收货
	$api->post('wechat/depot/confirm', 'DepotController@CustomerDepotnoteSetok');
	//商家登录
	$api->post('tenant/login', 'AuthorizationController@signin');
	//微信登陆
	$api->post('wechat/login', 'WechatController@WechatLogin');
	
	//获取openid
	$api->post('wechat/getid', 'WechatController@WechatGetid');
	//快递查询
	$api->post('express/chech', 'EbusinessController@getOrderTracesByJson');
	//快递查询
	$api->get('express/list', 'EbusinessController@GetExpressList');
	

	//公用函数
		//获取客户类型分类
	$api->get('custype/list', 'PfonController@CustomerTypeList');
		//7天自动收货
	$api->get('depot/autook', 'DepotController@AutoDepotnoteSetok');
		//获取快速报价单零件
	$api->post('fastorder/get', 'PfonController@GetFastOrder');
	

	
    // 需要 token 验证的接口
    $api->group(['middleware' => 'custom.jwt.auth:api'], function($api) {
        // 当前登录管理员信息
        $api->get('admin', 'AdminsController@me');
		//获取商家广告
		$api->post('tenant/ad/get', 'SyssetingController@AdGet');
		
		//商家管理-编辑部门用户
		$api->post('manage/depuser/edit/{id}', 'UsersController@EditUser');
		//商家管理-删除部门用户
		$api->post('manage/depuser/del', 'UsersController@DeleUser');
		//商家管理-添加部门用户
		$api->post('manage/depuser/add', 'UsersController@AddUser');
		//商家管理-部门用户列表
		$api->get('manage/depuser/list', 'UsersController@ListUser');
		//商家管理-添加权限
		$api->post('manage/userrole/add', 'UsersController@AddUserRole');
		//商家管理-部门列表
		$api->get('manage/depart/list', 'UsersController@ListDepartment');
		//商家管理-获取权限名称列表
		$api->get('manage/userrole/list', 'UsersController@UserRoleList');
		//商家管理-获取权限
		$api->post('manage/userrole/get', 'UsersController@UserRoleGet');
		//商家管理-删除权限
		$api->post('manage/userrole/del', 'UsersController@UserRoleDel');
		//用户绑定微信
		$api->post('wechat/bind', 'WechatController@WechatBind');
		//用户解除绑定微信
		$api->post('wechat/untying', 'WechatController@WechatUntying');
	
		//信息部获取客户列表
		$api->get('info/list', 'InformationController@CustomerList');
		//信息部添加客户
		$api->post('info/add', 'InformationController@CustomerAdd');
		//信息部修改客户
		$api->post('info/edit/{id}', 'InformationController@CustomerEdit');
		//信息部删除客户
		$api->get('info/del/{id}', 'InformationController@CustomerDel');
		//信息部删除客户联系人
		$api->get('info/dellxr/{id}', 'InformationController@CustomerDellxr');
		//信息部设置默认联系人
		$api->post('info/linkman/default', 'InformationController@SetDefaultLK');
		//信息部添加成型材质
		$api->post('info/material/add', 'InformationController@AddMaterials');
		//信息部修改成型材质
		$api->post('info/material/edit/{id}', 'InformationController@EditMaterials');
		//成型材质列表
		$api->get('info/material/list', 'InformationController@ListMaterials');
		//成型材质列表
		$api->get('info/material/listall', 'InformationController@ListMaterialsAll');
		//信息部添加成型工艺
		$api->post('info/moldi/add', 'InformationController@AddMolding');
		//信息部修改成型工艺
		$api->post('info/moldi/edit/{id}', 'InformationController@EditMolding');
		//成型工艺列表
		$api->get('info/moldi/list', 'InformationController@ListMolding');
		//成型工艺列表
		$api->get('info/moldi/listall', 'InformationController@ListMoldingAll');
		//信息部添加表面处理
		$api->post('info/surface/add', 'InformationController@AddSurface');
		//信息部修改表面处理
		$api->post('info/surface/edit/{id}', 'InformationController@EditSurface');
		//表面处理列表
		$api->get('info/surface/list', 'InformationController@ListSurface');
		//表面处理列表
		$api->get('info/surface/listall', 'InformationController@ListSurfaceAll');
		//信息部添加成型设备
		$api->post('info/equipment/add', 'InformationController@AddEquipment');
		//信息部修改成型设备
		$api->post('info/equipment/edit/{id}', 'InformationController@EditEquipment');
		//成型设备列表
		$api->get('info/equipment/list', 'InformationController@ListEquipment');
		//成型设备列表
		$api->get('info/equipment/listall', 'InformationController@ListEquipmentAll');
		//信息部修改成型设备
		$api->post('info/deles', 'InformationController@DelInform');
		
		//销售部获取订单列表
		$api->get('SalesOrderList', 'SalesOrderController@SalesOrderList');
		//销售部-添加订单-获取客户列表
		$api->get('sales/order/customlist', 'SalesOrderController@ListCustom');
		//销售部-添加订单-获取客户联系人
		$api->get('sales/order/customlinkman/{id}', 'SalesOrderController@ListLinkman');
		//销售部-添加订单-获取部门用户列表
		$api->get('sales/order/depusers', 'SalesOrderController@ListDepusers');
		//销售部-添加订单-上传文件
		$api->post('sales/order/upload', 'SalesOrderController@FileUpload');
		$api->post('sales/order/add', 'SalesOrderController@OrderAdd');
		//销售部-添加订单-根据工艺获取成型设备
		$api->get('sales/equipment/list/{id}', 'SalesOrderController@ListEquipment');
		//销售部-添加订单-根据工艺获取成型材料
		$api->get('sales/material/list/{id}', 'SalesOrderController@ListMaterials');
		//销售部-获取订单报价单
		$api->get('sales/order/quotation/{id}', 'SalesOrderController@GetQuotations');
		//销售部-更新订单报价单
		$api->post('sales/order/quotation/update/{id}', 'SalesOrderController@UpdateQuotations');
		//销售部-更新订单报价单
		$api->post('sales/order/quotation/updateqrurl', 'SalesOrderController@UpdateQuotationsQrurl');
		//销售部-批量审核报价单
		$api->post('sales/order/quotation/chack', 'SalesOrderController@ChackOrder');
		//销售部-获取订单列表
		$api->get('sales/order/list', 'SalesOrderController@GetOrders');
		//销售部-修改订单
		$api->post('sales/order/edit/{id}', 'SalesOrderController@EditOrder');
		//销售部-删除订单
		$api->get('sales/order/del/{id}', 'SalesOrderController@DelOrder');
		//销售部-查看订单评价
		$api->post('sales/order/evaluate', 'SalesOrderController@LookOrderEvaluate');
		//销售部-评价列表
		$api->post('sales/order/evaluate/list', 'SalesOrderController@OrderEvaluateList');
		//销售部-售后列表
		$api->post('sales/order/after/list', 'SalesOrderController@OrderAfterSale');
		//销售部-审核售后
		$api->post('sales/order/after/check', 'SalesOrderController@OrderAfterSaleCheck');
		//销售部-再次下单功能-获取订单零件列表
		$api->post('sales/order/reistparts', 'SalesOrderController@RloadOrderPartsList');
		//销售部-再次下单功能-提交订单
		$api->post('sales/order/reloadorder', 'SalesOrderController@RloadOrderAdd');
		//销售部-新的未处理售后数量
		$api->get('sales/order/after/count', 'SalesOrderController@OrderAfterSaleNewNum');
		
		
		//判断商家信息是否完整
		$api->get('tenant/chackinfo', 'SyssetingController@CheckTenantInfo');
		//获取商家分类
		$api->get('tenant/type', 'SyssetingController@GetTenantType');
		//获取商家信息
		$api->get('tenant/info', 'SyssetingController@GetTenant');
		//设置商家信息
		$api->post('tenant/setinf', 'SyssetingController@SetTenant');
		//获取商家等级信息
		$api->get('tenant/level', 'SyssetingController@TenantLevel');
		//获取商家等级信息
		$api->get('tenant/level/list', 'SyssetingController@TenantLevelList');
		//商家上传广告图片
		$api->post('tenant/ad/set', 'SyssetingController@AdUpdate');
		//获取和设置商家单据模版
		$api->post('tenant/tmpl', 'SyssetingController@TenantTemp');
		//获取商家过期信息
		$api->get('tenant/expired', 'SyssetingController@TenantExpired');
		//商家提升等级
		$api->post('tenant/levelup', 'SyssetingController@TenantLevelUp');
		//商家支付订单查询结果
		$api->post('tenant/orderquery', 'SyssetingController@OrderQuery');
		//商家支付订单列表
		$api->post('tenant/payorderlist', 'SyssetingController@WechatPayOrderList');
		//商家申请发票
		$api->post('tenant/invoice/send', 'PfonController@AddInvoices');
		
		//生产部待加工零件库列表
		$api->get('prod/parts/list', 'ProductController@PartList');
		//生产部生产任务书添加
		$api->post('prod/manuf/add', 'ProductController@AddManufacture');
		//生产部生产任务书获取
		$api->post('prod/manuf/get/{id}', 'ProductController@GetManufacture');
		//生产部生产任务书获取
		$api->get('prod/manuf/chack/{id}', 'ProductController@ChackManufacture');
		//生产部获取生产任务书列表
		$api->get('prod/manuf/list', 'ProductController@GetManufactures');
		//生产部查看生产计划
		$api->post('prod/propla/get/{id}', 'ProductController@GetProductPlan');
		//生产部查看生产计划
		$api->post('prod/propla/setok', 'ProductController@SetProductPlanOk');
//生产部上传生产完成图片
		$api->post('prod/propla/uppics', 'ProductController@UploadPic');
		$api->post('prod/propla/delpic', 'ProductController@DeletePic');
		$api->post('prod/propla/getpics', 'ProductController@GetPic');

		//仓管部完成零件列表
		$api->get('depot/parts/list', 'DepotController@PartList');
		//仓管部 添加送货单
		$api->post('depot/parts/send', 'DepotController@AddDepotnote');
		//仓管部送货单列表
		$api->get('depot/delin/list', 'DepotController@DepotnoteList');
		//仓管部设置送货单为完成状态
		$api->post('depot/setok', 'DepotController@DepotnoteSetok');
		//仓管部设置送货单为完成状态
		$api->get('depot/get/{id}', 'DepotController@GetDepotnote');
		
		//统计
		$api->post('statistics/order', 'StatisticsController@AllOrder');
		$api->post('statistics/custome', 'StatisticsController@AllCustomer');
		$api->post('statistics/part', 'StatisticsController@AllPart');
		$api->post('statistics/info', 'StatisticsController@AllInfo');
		$api->post('statistics/after', 'StatisticsController@AllReturn');
		$api->post('statistics/byday', 'StatisticsController@ByDay');
		$api->post('statistics/byhour', 'StatisticsController@ByHour');
		$api->post('statistics/bymoon', 'StatisticsController@ByMoon');
    });
	
});
