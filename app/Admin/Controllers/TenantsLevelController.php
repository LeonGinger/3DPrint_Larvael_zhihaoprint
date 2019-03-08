<?php

namespace App\Admin\Controllers;

use App\Models\TenantLevel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class TenantsLevelController extends Controller
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
            ->header('商家等级')
            ->description(' ')
            ->body($this->grid()->disableFilter()->disableRowSelector()->disableExport()->render());
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
            ->header('详情')
            ->description(' ')
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
            ->header('编辑')
            ->description(' ')
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
            ->header('创建')
            ->description(' ')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TenantLevel);
        $grid->id('编号');
        $grid->name('等级名称');
        $grid->customer_numbers('客户数量');
        $grid->order_numbers('订单数量');
        $grid->quarter_price('季度付费金额');
        $grid->year_price('年度付费金额');
        $grid->column('is_enable', '启用状态')->display(function($is_enable) {
            return $is_enable ? '<span class="label label-success">启用</span>' : '<span class="label label-danger">禁用</span>';
        });
        $grid->created_at('创建时间');
        $grid->updated_at('更新时间');

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
        $show = new Show(TenantLevel::findOrFail($id));

        $show->id('Id');
        $show->name('等级名称');
        $show->customer_numbers('客户数量');
        $show->order_numbers('订单数量');
        $show->quarter_price('季度付费金额');
        $show->year_price('年度付费金额');
        $show->is_enable('是否启用');
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
        $form = new Form(new TenantLevel);
        $states = [
            'on' => ['value' => 1, 'text' => '启用', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => '禁用', 'color' => 'danger'],
        ];
        $form->text('name', '等级名称   ')->rules(['required']);
        $form->number('customer_numbers', '客户数量')
            ->rules(['required']);
        $form->number('order_numbers', '订单数量')
            ->rules(['required']);
        $form->decimal('quarter_price', '季度付费金额')
            ->rules(['required']);
        $form->decimal('year_price', '年度付费金额')
            ->rules(['required']);
        $form->switch('is_enable', '启用状态')->states($states)->default(1);
        return $form;
    }
}
