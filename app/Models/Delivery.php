<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    //
	protected $table = 'deliverys';
	protected $fillable = [
		'part_id',
		'delivery_note_id',
		'log',
		'yscount',
		'status'
	];
}
