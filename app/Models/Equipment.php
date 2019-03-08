<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
	protected $table = 'equipments';

	protected $fillable = [
	    'tenat_id',
	    'mold_id',
	    'mname',
	    'marc',
	    'mmaker',
	    'status',
    ];

	public function Molding() {
	    return $this->belongsTo(Molding::class, 'mold_id', 'id');
    }
}
