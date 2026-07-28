<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 
        'status', 'published_at', 'is_featured', 'created_by', 'penulis', 'thumbnail', 'news_category_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
