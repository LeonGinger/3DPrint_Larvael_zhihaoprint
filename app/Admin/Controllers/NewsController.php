<?php

namespace App\Admin\Controllers;

use Storage;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\NewsType;
use App\Models\News;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Content $content)
    {
        $news_list = News::orderByDesc('sort')->get();
        return $content
            ->header('新闻')
            ->description('新闻列表')
            ->body(new Box('新闻', view('admin.news.list.index', compact('news_list'))));
    }

    public function create(Content $content)
    {
        $news_types = NewsType::all();
        return $content
            ->header('创建新闻')
            ->description('创建一个新的新闻')
            ->body(new Box('创建新闻', view('admin.news.list.create', compact('news_types'))));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|min:2',
            'sub_title' => 'string|min:2|nullable',
            'news_type_id' => 'required|exists:news_types,id',
            'author' => 'string|nullable',
            'views_count' => 'numeric',
            'sort' => 'numeric',
            'is_show' => 'required|boolean',
            'content' => 'string|nullable',
        ]);

        $cred = $request->only('title', 'sub_title', 'news_type_id', 'author', 'views_count', 'sort', 'is_show', 'content');
        News::create($cred);
        admin_success('提示', '操作成功');
        return redirect()->back();
    }

    public function edit(Content $content, $id)
    {
        $news_types = NewsType::all();
        $news = News::findOrFail($id);
        $title = $news->title;
        return $content
            ->header("编辑{$title}")
            ->description('编辑新闻')
            ->body(new Box($title, view('admin.news.list.edit', compact('news', 'news_types'))));
    }

    public function update($id, Request $request)
    {
        $news_type = News::findOrFail($id);
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
        $news_type = News::findOrFail($id);
        $news_type->news()->delete();
        $news_type->delete();
        admin_success('提示', '操作成功');
        return redirect()->back();
    }
}