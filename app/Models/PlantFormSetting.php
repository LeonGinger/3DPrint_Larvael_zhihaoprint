<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantFormSetting extends Model
{
    protected $fillable = [
        'baojia_template_text1',
        'baojia_template_text2',
        'ad_header_url',
        'ad_footer_url',
    ];
}
