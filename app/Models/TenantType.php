<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantType extends Model
{
    protected $fillable = [
        'name',
        'intro'
    ];
}
