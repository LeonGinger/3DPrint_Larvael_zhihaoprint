<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model implements JWTSubject
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = [
        'address',
        'name',
        'id',
        'tel',
        'tenant_id',
        'no',
        'status'
    ];
	
	public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
