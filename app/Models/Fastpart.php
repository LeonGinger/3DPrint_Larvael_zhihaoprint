<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fastpart extends Model
{
    protected $fillable = [
        'id',
        'fq_id',
        'material_id',
        'name',
        'diagram',
        'volume_size',
		'status',
		'coefficient',
		'price',
		'product_num',
		'requirements',
		'remark'
    ];
}
