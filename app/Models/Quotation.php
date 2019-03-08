<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    //
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = [
		'order_id',
		'no',
		'total',
		'qs',
		'qt',
		'status',
		'qrcode_url',
		'log',
	];
}
