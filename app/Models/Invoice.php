<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'tenat_id',
        'rise',
        'tax_number',
        'addressee',
        'phone',
        'province',
        'city',
        'region',
        'address',
        'base_account_number',
        'base_account_bank',
        'company_number',
        'company_register_address',
        'status',
        'feedback',
        'file_url',
        'handle_time',
    ];

    public function tenat()
    {
        return $this->belongsTo(Tenant::class);
    }
}
