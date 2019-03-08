<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_plan extends Model
{
    //
	protected $table = 'product_plans';
	protected $fillable = [
		'manufacture_id',
		'part_id',
		'jh_data',
		'start_data',
		'end_data',
		'pics',
		'status',
	];
}
