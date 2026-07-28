<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbliSection extends Model
{
    use HasFactory;

    protected $table = 'kbli_sections';

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(KbliCategory::class, 'category_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(KbliItem::class, 'section_id', 'id');
    }
}
