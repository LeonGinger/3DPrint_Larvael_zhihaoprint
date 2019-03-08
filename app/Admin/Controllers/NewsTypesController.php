<?php

namespace App\Admin\Controllers;

use Storage;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\NewsType;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class NewsTypesController extends Controller
{
    public function index(Content $content)
    {
        $news_types = NewsType::orderByDesc('sort')->get();
        return $content
            ->header('新闻类型')
            ->description('新闻类型列表')
            ->body(new Box('新闻类型', view('admin.news.types.list', compact('news_types'))));
    }

    public function create(Content $content)
    {
        return $content
            ->header('创建新闻类型')
            ->description('创建一个新的新闻类型')
            ->body(new Box('创建新闻类型', view('admin.news.types.create')));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|min:2',
            'is_enable' => 'required|boolean',
        ]);

        $cred = $request->only('name', 'is_enable', 'sort');
        NewsType::create($cred);
        admin_success('提示', '操作成功');
        return redirect()->back();
    }

    public function edit(Content $content, $id)
    {
        $news_type = NewsType::findOrFail($id);
        $title = $news_type->name;
        return $content
            ->header("编辑{$title}")
            ->description('编辑新闻类型')
            ->body(new Box($title, view('admin.news.types.edit', compact('news_type'))));
    }

    public function update($id, Request $request)
    {
        $news_type = NewsType::findOrFail($id);
        $this->validate($request, [
            'name' => 'required|string|min:2',
            'is_enable' => 'required|boolean',
        ]);

        $cred = $request->only('name', 'is_enable', 'sort');
        $news_type->update($cred);
        admin_success('提示', '操作成功');
        return redirect()->back();
    }

    public function destroy($id)
    {
        $news_type = NewsType::findOrFail($id);
        $news_type->news()->delete();
        $news_type->delete();
        admin_success('提示', '操作成功');
        return redirect()->back();
    }
}