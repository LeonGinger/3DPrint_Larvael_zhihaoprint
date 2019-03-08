<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Molding extends Model
{
	protected $table = 'molding_processes';

	protected $fillable = [
	    'tenat_id',
	    'name',
        'status',
    ];

	public function equipments()
    {
        return $this->hasMany(Equipment::class, 'mold_id', 'id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'mold_id', 'id');
    }
}
