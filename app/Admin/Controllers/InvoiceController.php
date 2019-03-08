<?php

namespace App\Admin\Controllers;

use Storage;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Invoice;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function applyList(Content $content, Request $request)
    {
        $rise = $request->get('rise');
        $status = $request->get('status');

        $query = Invoice::with('tenat');
        if ($status) {
            $query->where('status', $status);
        }
        if ($rise) {
            $query->where('rise', 'LIKE', '%' . $rise . '%');
        }
        $invoices = $query
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $content
            ->header('发票列表')
            ->description(' ')
            ->body(new Box(' ', view('admin.invoice.apply-list', compact('invoices'))));
    }

    public function show($id, Content $content)
    {
        $invoice = Invoice::findOrFail($id);
        $title = $invoice->rise;
        return $content
            ->header('技术服务费发票信息')
            ->description(" ")
            ->body(new Box(' ', view('admin.invoice.show', compact('invoice'))));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:1,2,3,4',
            'feed_file' => 'mimes:pdf|nullable',
            'feedback' => 'string|nullable'
        ]);

        $invoice = Invoice::findOrFail($id);
        $cred = $request->only('status', 'feedback');
        $cred = array_filter($cred);
        $cred['handle_time'] = now()->toDateTimeString();
        $invoice->update($cred);
        if ($request->feed_file) {
            $extension = $request->feed_file->getClientOriginalExtension();
            $filename = rand(11111111, 99999999). '.' . $extension;
            $request->file('feed_file')->move(
                base_path() . '/public/files/pdf/', $filename
            );
            $invoice->file_url = asset('/files/pdf/' . $filename);
            $invoice->save();
        }
        admin_success('提示', '操作成功');
        return redirect()->back();
    }

}
