<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Tenant extends Authenticatable implements JWTSubject
{
    use Notifiable;

    const STATUS_ACTIVE = 1; // 启用状态
    const STATUS_INACTIVE = 0; // 禁用状态

    protected $fillable = [
        'name',
        'linkman',
        'phone',
        'password',
        'expired_at',
        'tenant_level_id',
        'tenant_type_id',
        'status',
        'weapp_openid',
    ];

    public function tenantLevel()
    {
        return $this->belongsTo(TenantLevel::class);
    }

    public function tenantType()
    {
        return $this->belongsTo(TenantType::class);
    }

    public static function getBelongsToSelectOptions($relation_table_name)
    {
        $options = \DB::table($relation_table_name)->select('id','name as text')->get();
        $selectOption = [];
        foreach ($options as $option){
            $selectOption[$option->id] = $option->text;
        }
        return $selectOption;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'user_type' =>  'tenant',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'tenat_id', 'id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'tenat_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'tenat_id', 'id');
    }
}
