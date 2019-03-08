<?php

namespace App\Admin\Controllers;

use App\Models\Tenant;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Dingo\Api\Http\Request;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TenantsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商家列表')
            ->description(' ')
            ->body($this->grid()->disableRowSelector()->disableExport()->render());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('商家信息')
            ->description('查看商家信息')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商家')
            ->description('修改商家信息')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('新增商家')
            ->description('添加一个新的商家')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Tenant);
        $grid->id('编号')->sortable();
        $grid->column('phone', '手机号码');
        $grid->column('name', '商家名称');
        $grid->column('linkman', '联系人');
        $grid->column('tenantLevel.name', '商家等级');
        $grid->column('tenantLevel.customer_numbers', '限制客户数');
        $grid->column('tenantLevel.order_numbers', '限制订单数');
        $grid->column('tenantLevel.quarter_price', '季度付费金额');
        $grid->column('tenantLevel.year_price', '年度付费金额');
        $grid->column('status', '启用状态')->display(function($status) {
            return $status ? '<span class="label label-success">启用</span>' : '<span class="label label-danger">禁用</span>';
        });
        $grid->column('expired_at', '状态')->display(function($expired_at) {
            return Carbon::now()->lt(Carbon::parse($expired_at)) ? '<span class="label label-success">正常</span>' : '<span class="label label-danger">过期</span>';
        });
        $grid->column('expired_date', '过期时间')->display(function () {
            return $this->expired_at;
        });

        $grid->filter(function($filter){
            $filter->setName('查询');
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->column(1/2, function ($filter) {
                $filter->like('phone', '手机号码');
                $filter->like('name', '商家名称');
                $filter->like('linkman', '联系人');
            });
            $filter->column(1/2, function ($filter) {
                $filter->between('expired_at', '过期时间')->datetime();
                $filter->equal('status', '启用状态')->radio([
                    1 => '是',
                    0 => '否',
                ]);
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Tenant::findOrFail($id));
        $show->id('编号');
        $show->phone('手机号码');
        $show->name('名称');
        $show->linkman('联系人');
        $show->status('启用');
        $show->expired_at('过期时间');
        $show->created_at('创建时间');
        $show->updated_at('更新时间');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Tenant);
        $form->text('name', '名称')->rules('required|string');
        $form->text('phone', '手机号码')->rules('required|string');
        $form->password('password', '密码')->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });
        $form->ignore(['password_confirmation']);

        $form->text('linkman', '联系人')->rules('required|string');
        $states = [
            'on' => ['value' => 1, 'text' => '启用', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '禁用', 'color' => 'danger'],
        ];
        $form->select('tenant_level_id', '商家等级')
            ->options(Tenant::getBelongsToSelectOptions('tenant_levels'))->rules(['required']);
        $form->select('tenant_type_id', '商家行业')
            ->options(Tenant::getBelongsToSelectOptions('tenant_types'))->rules(['required']);
        $form->switch('status', '启用状态')->states($states)->default(1);
        $form->datetime('expired_at', '过期时间')->rules('required');
        $form->display('weapp_openid', '微信OPENID');
        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });
        return $form;
    }
}
