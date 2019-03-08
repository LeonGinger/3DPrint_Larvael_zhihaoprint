<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Changelog extends Model
{
    //
	protected $fillable = [
        'part_id',
        'change',
        'product_num',
        'remark',
    ];
}
