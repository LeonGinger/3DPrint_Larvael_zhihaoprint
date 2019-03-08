<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Materials;
use App\Models\Molding;
use App\Models\Tenant;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class MaterialsController extends Controller
{
    public function index(Content $content)
    {
        $materials = Materials::with(['Molding'])->orderByDesc('created_at')->paginate(10);
        foreach ($materials as $material) {
            if ($material->tenat_id) {
                $material->tenant = Tenant::find($material->tenat_id);
            } else {
                $material->tenant = null;
            }
        }
        return $content
            ->header('成型材料')
            ->description(' ')
            ->body(new Box(' ', view('admin.production-information.materials.index', compact('materials'))));
    }

    public function create(Content $content)
    {
        $tenants = Tenant::all();
        $moldings = Molding::all();
        return $content
            ->header('添加成型材料')
            ->description(" ")
            ->body(new Box(' ', view('admin.production-information.materials.create', compact('tenants','moldings'))));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required|numeric',
            'density' => 'required|numeric',
            'shape' => 'required|in:粉体,固体,液体',
            'mold_id' => 'required|exists:molding_processes,id',
            'status' => 'required|in:0,1',
        ]);
        $cred = $request->only('name', 'price', 'density', 'shape', 'status', 'mold_id', 'tenat_id');
        Materials::create($cred);
        admin_success('提示', '操作成功');
        return redirect(route('admin.materials.index'));
    }

    public function edit($id, Content $content)
    {
        $tenants = Tenant::all();
        $moldings = Molding::all();
        $material = Materials::findOrFail($id);
        $title = $material->name;
        return $content
            ->header($title)
            ->description(" ")
            ->body(new Box(" ", view('admin.production-information.materials.edit', compact('material', 'tenants', 'moldings'))));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required|numeric',
            'density' => 'required|numeric',
            'shape' => 'required|in:粉体,固体,液体',
            'mold_id' => 'required|exists:molding_processes,id',
            'status' => 'required|in:0,1',
        ]);
        $cred = $request->only('name', 'price', 'density', 'shape', 'status', 'mold_id', 'tenat_id');
        $material = Materials::findOrFail($id);
        $material->update($cred);
        admin_success('提示', '操作成功');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $material = Materials::findOrFail($id);
        $material->delete();
        admin_success('提示', '操作成功');
        return redirect(route('admin.materials.index'));
    }
}