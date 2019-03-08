<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    //
	protected $fillable = [
		'order_id',
		'material_id',
		'surface_id',
		'molding_process_id',
		'equipment_id',
		'name',
		'diagram',
		'volume_size',
		'coefficient',
		'price',
		'product_num',
		'start_date',
		'due_date',
		'remark',
		'status',
	];
}
