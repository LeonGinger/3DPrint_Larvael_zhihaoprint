<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsType extends Model
{
    protected $fillable = [
        'name',
        'sort',
        'is_enable',
    ];

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
