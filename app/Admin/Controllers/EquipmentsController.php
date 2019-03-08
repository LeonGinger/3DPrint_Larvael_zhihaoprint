<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Molding;
use App\Models\Tenant;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class EquipmentsController extends Controller
{
    public function index(Content $content)
    {
        $equipments = Equipment::with(['Molding'])->orderByDesc('created_at')->paginate(10);
        foreach ($equipments as $equipment) {
            if ($equipment->tenat_id) {
                $equipment->tenant = Tenant::find($equipment->tenat_id);
            } else {
                $equipment->tenant = null;
            }
        }
        return $content
            ->header('生产设备')
            ->description(' ')
            ->body(new Box(' ', view('admin.production-information.equipments.index', compact('equipments'))));
    }

    public function create(Content $content)
    {
        $tenants = Tenant::all();
        $moldings = Molding::all();
        return $content
            ->header('添加生产设备')
            ->description(" ")
            ->body(new Box(' ', view('admin.production-information.equipments.create', compact('tenants','moldings'))));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'mname' => 'required',
            'marc' => 'required',
            'mmaker' => 'required',
            'mold_id' => 'required|exists:molding_processes,id',
            'status' => 'required|in:0,1',
        ]);
        $cred = $request->only('mname', 'marc', 'mmaker', 'status', 'mold_id', 'tenat_id');
        Equipment::create($cred);
        admin_success('提示', '操作成功');
        return redirect(route('admin.equipments.index'));
    }

    public function edit($id, Content $content)
    {
        $tenants = Tenant::all();
        $moldings = Molding::all();
        $equipment = Equipment::findOrFail($id);
        $title = $equipment->mname;
        return $content
            ->header($title)
            ->description(" ")
            ->body(new Box(" ", view('admin.production-information.equipments.edit', compact('equipment', 'tenants', 'moldings'))));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'mname' => 'required',
            'marc' => 'required',
            'mmaker' => 'required',
            'mold_id' => 'required|exists:molding_processes,id',
            'status' => 'required|in:0,1',
        ]);
        $cred = $request->only('mname', 'marc', 'mmaker', 'status', 'mold_id', 'tenat_id');
        $equipment = Equipment::findOrFail($id);
        $equipment->update($cred);
        admin_success('提示', '操作成功');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();
        admin_success('提示', '操作成功');
        return redirect(route('admin.equipments.index'));
    }
}