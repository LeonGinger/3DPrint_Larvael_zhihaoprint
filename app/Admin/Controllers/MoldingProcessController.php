<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Molding;
use App\Models\Tenant;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class MoldingProcessController extends Controller
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        $molding_processes = Molding::orderByDesc('created_at')->paginate(10);
        foreach ($molding_processes as $molding_process) {
            if ($molding_process->tenat_id) {
                $molding_process->tenant = Tenant::find($molding_process->tenat_id);
            } else {
                $molding_process->tenant = null;
            }
        }
        return $content
            ->header('生产工艺')
            ->description(' ')
            ->body(new Box(' ', view('admin.production-information.molding-processes.index', compact('molding_processes'))));
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
        $tenants = Tenant::all();
        $molding_process = Molding::findOrFail($id);
        $title = $molding_process->name;
        return $content
            ->header($title)
            ->description(" ")
            ->body(new Box(" ", view('admin.production-information.molding-processes.edit', compact('molding_process', 'tenants'))));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $tenants = Tenant::all();
        return $content
            ->header('添加工艺')
            ->description(" ")
            ->body(new Box(' ', view('admin.production-information.molding-processes.create', compact('tenants'))));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|in:0,1',
        ]);
        $cred = $request->only('name', 'status', 'tenat_id');
        Molding::create($cred);
        admin_success('提示', '操作成功');
        return redirect(route('admin.molding_processes.index'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|in:0,1',
        ]);
        $cred = $request->only('name', 'status', 'tenat_id');
        $molding_process = Molding::findOrFail($id);
        $molding_process->update($cred);
        admin_success('提示', '操作成功');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $molding_process = Molding::findOrFail($id);
        // 检测该工艺下是否有生产设备
        if ($molding_process->equipments->isNotEmpty()) {
            admin_error('提示', '请删除该工艺下所有生成设备再操作');
            return redirect()->back();
        }
        // 检测该工艺下是否有成型材料
        if ($molding_process->materials->isNotEmpty()) {
            admin_error('提示', '请删除该工艺下所有成型材料再操作');
            return redirect()->back();
        }

        $molding_process->delete();
        admin_success('提示', '操作成功');
        return redirect(route('admin.molding_processes.index'));
    }
}
