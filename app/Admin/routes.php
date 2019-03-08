<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('/tenant_data', 'HomeController@tenantData');
    $router->resource('auth/users', 'UserController');

    $router->group(['prefix' => 'tenant'], function($router) {
        $router->get('lists', 'HomeController@tenantLists')->name('admin.tenant.list');
        $router->get('export_orders', 'HomeController@exportTenantOrders')->name('admin.tenant.orders.export');
    });
    $router->resource('tenants', TenantsController::class);
    $router->resource('tenants_level', TenantsLevelController::class);
    $router->resource('tenants_type', TenantTypesController::class);


    $router->group(['prefix' => 'plantform_settings'], function($router) {
        $router->get('ad_settings', 'PlantFormSettingsController@showAdSettings')->name('admin.plantform_settings.ad.show');
        $router->post('ad_settings', 'PlantFormSettingsController@storeAdSettings')->name('admin.plantform_settings.ad.save');
        $router->get('baojia_template', 'PlantFormSettingsController@showBaojiaTemplate')->name('admin.plantform_settings.baojia.show');
        $router->post('baojia_template', 'PlantFormSettingsController@saveBaojiaTemplate')->name('admin.plantform_settings.baojia.save');
    });

    // 生产信息管理
    $router->group(['prefix' => 'production_information'], function($router) {
        // 生产工艺
        $router->group(['prefix' => 'molding_processes'], function($router) {
            $router->get('/', 'MoldingProcessController@index')->name('admin.molding_processes.index');
            $router->post('/', 'MoldingProcessController@store')->name('admin.molding_processes.store');
            $router->get('create', 'MoldingProcessController@create')->name('admin.molding_processes.create');
            $router->get('{id}/edit', 'MoldingProcessController@edit')->name('admin.molding_processes.edit');
            $router->post('{id}', 'MoldingProcessController@update')->name('admin.molding_processes.update');
            $router->delete('{id}', 'MoldingProcessController@destroy')->name('admin.molding_processes.destroy');
        });
        // 生产设备
        $router->group(['prefix' => 'equipments'], function($router) {
            $router->get('/', 'EquipmentsController@index')->name('admin.equipments.index');
            $router->get('create', 'EquipmentsController@create')->name('admin.equipments.create');
            $router->post('/', 'EquipmentsController@store')->name('admin.equipments.store');
            $router->get('{id}/edit', 'EquipmentsController@edit')->name('admin.equipments.edit');
            $router->post('{id}', 'EquipmentsController@update')->name('admin.equipments.update');
            $router->delete('{id}', 'EquipmentsController@destroy')->name('admin.equipments.destroy');
        });
        // 成型材料
        $router->group(['prefix' => 'materials'], function($router) {
            $router->get('/', 'MaterialsController@index')->name('admin.materials.index');
            $router->get('create', 'MaterialsController@create')->name('admin.materials.create');
            $router->post('/', 'MaterialsController@store')->name('admin.materials.store');
            $router->get('{id}/edit', 'MaterialsController@edit')->name('admin.materials.edit');
            $router->post('{id}', 'MaterialsController@update')->name('admin.materials.update');
            $router->delete('{id}', 'MaterialsController@destroy')->name('admin.materials.destroy');
        });
        // 表面处理
        $router->group(['prefix' => 'surfaces'], function($router) {
            $router->get('/', 'SurfacesController@index')->name('admin.surfaces.index');
            $router->get('create', 'SurfacesController@create')->name('admin.surfaces.create');
            $router->post('/', 'SurfacesController@store')->name('admin.surfaces.store');
            $router->get('{id}/edit', 'SurfacesController@edit')->name('admin.surfaces.edit');
            $router->post('{id}', 'SurfacesController@update')->name('admin.surfaces.update');
            $router->delete('{id}', 'SurfacesController@destroy')->name('admin.surfaces.destroy');
        });
    });

    // 发票管理
    $router->group(['prefix' => 'invoice'], function($router) {
        $router->get('apply_list', 'InvoiceController@applyList')->name('admin.invoice.apply_list');
        $router->get('apply_list/{id}', 'InvoiceController@show')->name('admin.invoice.show');
        $router->post('apply_list/{id}', 'InvoiceController@update')->name('admin.invoice.update');
    });
    // 新闻管理
    $router->group(['prefix' => 'news'], function($router) {
        $router->group(['prefix' => 'types'], function($router) {
            $router->get('/', 'NewsTypesController@index')->name('admin.news.types');
            $router->post('/', 'NewsTypesController@store')->name('admin.news.types.store');
            $router->get('create', 'NewsTypesController@create')->name('admin.news.types.create');
            $router->get('/{id}/edit', 'NewsTypesController@edit')->name('admin.news.types.edit');
            $router->post('/{id}', 'NewsTypesController@update')->name('admin.news.types.update');
            $router->delete('/{id}', 'NewsTypesController@destroy')->name('admin.news.types.destroy');
        });
        $router->group(['prefix' => 'list'], function($router) {
            $router->get('/', 'NewsController@index')->name('admin.news.list');
            $router->post('/', 'NewsController@store')->name('admin.news.list.store');
            $router->get('create', 'NewsController@create')->name('admin.news.list.create');
            $router->get('/{id}/edit', 'NewsController@edit')->name('admin.news.list.edit');
            $router->post('/{id}', 'NewsController@update')->name('admin.news.list.update');
            $router->delete('/{id}', 'NewsController@destroy')->name('admin.news.list.destroy');
        });
    });
});
