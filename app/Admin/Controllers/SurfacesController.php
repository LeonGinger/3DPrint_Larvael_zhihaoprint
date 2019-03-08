<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Surfaces;
use App\Models\Molding;
use App\Models\Tenant;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class SurfacesController extends Controller
{
    public function index(Content $content)
    {
        $surfaces = Surfaces::orderByDesc('created_at')->paginate(10);
        foreach ($surfaces as $surface) {
            if ($surface->tenat_id) {
                $surface->tenant = Tenant::find($surface->tenat_id);
            } else {
                $surface->tenant = null;
            }
        }
        return $content
            ->header('表面处理')
            ->description(' ')
            ->body(new Box(' ', view('admin.production-information.surfaces.index', compact('surfaces'))));
    }

    public function create(Content $content)
    {
        $tenants = Tenant::all();
        return $content
            ->header('添加表面处理')
            ->description(" ")
            ->body(new Box(' ', view('admin.production-information.surfaces.create', compact('tenants'))));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|in:0,1',
        ]);
        $cred = $request->only('name', 'status', 'tenat_id');
        Surfaces::create($cred);
        admin_success('提示', '操作成功');
        return redirect(route('admin.surfaces.index'));
    }

    public function edit($id, Content $content)
    {
        $tenants = Tenant::all();
        $surface = Surfaces::findOrFail($id);
        $title = $surface->name;
        return $content
            ->header($title)
            ->description(" ")
            ->body(new Box(" ", view('admin.production-information.surfaces.edit', compact('surface', 'tenants'))));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required|in:0,1',
        ]);
        $cred = $request->only('name', 'status', 'tenat_id');
        $surface = Surfaces::findOrFail($id);
        $surface->update($cred);
        admin_success('提示', '操作成功');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $surface = Surfaces::findOrFail($id);
        $surface->delete();
        admin_success('提示', '操作成功');
        return redirect(route('admin.surfaces.index'));
    }
}