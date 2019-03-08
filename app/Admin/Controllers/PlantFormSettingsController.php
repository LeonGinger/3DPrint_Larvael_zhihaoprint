<?php

namespace App\Admin\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\Controller;
use App\Models\PlantFormSetting;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;

class PlantFormSettingsController extends Controller
{
    public function showAdSettings(Content $content)
    {
        $plant_form_setting = PlantFormSetting::find(1);
        if (!$plant_form_setting) {
            $plant_form_setting = PlantFormSetting::create();
        }
        return $content
            ->header('广告设置')
            ->description('广告位设置免费用户的页眉页脚广告')
            ->body(new Box('广告设置', view('admin.plantform-settings.ad-settings', compact('plant_form_setting'))));
    }

    public function storeAdSettings(Request $request, ImageUploadHandler $handler)
    {
        $rules = [
            'ad_header_image' => 'sometimes|required|image|mimes:jpeg,jpg,png|max:20000',
            'ad_footer_image' => 'image|mimes:jpeg,jpg,png|max:20000|nullable',
        ];
        $messages = [
            'ad_header_image.image' => '页眉图片格式错误',
            'ad_header_image.max' => '页眉图片超过2M',
            'ad_footer_image.image' => '页脚图片格式错误',
            'ad_footer_image.max' => '页脚图片超过2M',
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            admin_error('提示', $validator->errors()->first());
            return redirect()->back();
        }
        $ad_header_image = $request->ad_header_image;
        $ad_footer_image = $request->ad_footer_image;
        $message = '操作成功';
        if (!$ad_header_image && !$ad_footer_image) {
            $message = '未上传任何图片';
        }
        $plant_form_setting = PlantFormSetting::find(1);
        if (!$plant_form_setting) {
            $plant_form_setting = PlantFormSetting::create();
        }

        if ($ad_header_image) {
            $folder = 'plantform_settings/ad_header_image';
            $rs = $handler->save($ad_header_image, $folder, '', 360);
            $path = $rs['path'];
            $plant_form_setting->ad_header_url = $path;
            $plant_form_setting->save();
            $message .= '，页眉图片已保存';
        }
        if ($ad_footer_image) {
            $folder = 'plantform_settings/ad_header_image';
            $rs = $handler->save($ad_footer_image, $folder, '', 360);
            $path = $rs['path'];
            $plant_form_setting->ad_footer_url = $path;
            $plant_form_setting->save();
            $message .= '，页脚图片已保存';
        }
        admin_success('提示', $message);
        return redirect()->route('admin.plantform_settings.ad.show');
    }

    public function showBaojiaTemplate(Content $content)
    {
        $plant_form_setting = PlantFormSetting::find(1);
        if (!$plant_form_setting) {
            $plant_form_setting = PlantFormSetting::create();
        }
        return $content
            ->header('报价模板')
            ->description('报价单页面的模版设置')
            ->body(new Box('报价模板设置', view('admin.plantform-settings.baojia-template', compact('plant_form_setting'))));
    }

    public function saveBaojiaTemplate(Request $request)
    {
        $rules = [
            'baojia_template_text1' => 'required|string',
            'baojia_template_text2' => 'required|string',
        ];
        $messages = [
            'baojia_template_text1.required' => '请输入零件检验质量标准模板文字',
            'baojia_template_text2.required' => '请输入货物交付以及结算区域模板文字',
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            admin_error('提示', $validator->errors()->first());
            return redirect()->back();
        }
        $message = '操作成功';
        $cred = $request->only('baojia_template_text1', 'baojia_template_text2');
        $plant_form_setting = PlantFormSetting::find(1);
        if (!$plant_form_setting) {
            $plant_form_setting = PlantFormSetting::create();
        }
        $plant_form_setting->update($cred);
        admin_success('提示', $message);
        return redirect()->route('admin.plantform_settings.baojia.show');
    }

}