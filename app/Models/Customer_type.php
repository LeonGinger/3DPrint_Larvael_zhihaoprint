<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;

class Customer_type extends Model implements JWTSubject
{
	protected $table = 'customer_types';
	protected $fillable = [
        'id',
        'pid',
        'name'
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
