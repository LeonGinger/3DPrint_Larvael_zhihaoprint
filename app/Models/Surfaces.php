<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surfaces extends Model
{
	protected $table = 'surfaces';

	protected $fillable = [
	    'name',
	    'status',
	    'tenat_id',
    ];
}
