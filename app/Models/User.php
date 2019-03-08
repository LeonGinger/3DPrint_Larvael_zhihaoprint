<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
	use SoftDeletes;
    protected $fillable = [
        'id',
        'name',
        'phone',
        'password',
        'sale',
        'openid',
        'last_login_at',
        'action_log',
    ];
	protected $dates = ['deleted_at'];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'user_type' =>  'user',
        ];
    }
}
