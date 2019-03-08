<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantLevel extends Model
{
    protected $fillable = [
        'name',
        'customer_numbers',
        'order_numbers',
//        'price',
        'quarter_price',
        'year_price',
        'is_enable',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
