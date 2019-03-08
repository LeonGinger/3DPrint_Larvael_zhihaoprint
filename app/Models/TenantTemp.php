<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantTemp extends Model
{
    protected $fillable = [
        'id',
        'tenant_id',
        'text1',
        'text2'
    ];
}
