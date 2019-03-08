<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;

class Materials extends Model implements JWTSubject
{
    protected $table = 'materials';
	protected $fillable = [
        'id',
        'name',
        'price',
        'density',
        'shape',
        'status',
        'tenat_id',
        'mold_id',
    ];
	
	public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function Molding() {
        return $this->belongsTo(Molding::class, 'mold_id', 'id');
    }
}
