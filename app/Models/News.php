<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title',
        'sub_title',
        'news_type_id',
        'content',
        'author',
        'views_count',
        'sort',
        'is_show',
    ];

    public function type()
    {
        return $this->belongsTo(NewsType::class);
    }
}
