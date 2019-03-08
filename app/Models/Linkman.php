<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;

class Linkman extends Model implements JWTSubject
{
	protected $table = 'linkmans';
	protected $fillable = [
        'id',
        'customer_id',
        'linkman_name',
        'lk_phone',
        'lk_address',
		'province',
		'city',
		'area',
		'zipcode',
		'email',
		'defvue',
        'lk_type'
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
