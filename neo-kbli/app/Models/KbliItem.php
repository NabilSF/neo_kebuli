<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbliItem extends Model
{
    use HasFactory;

    protected $table = 'kbli_items';

    protected $fillable = [
        'section_id',
        'code',
        'name',
        'description',
        'is_active'
    ];

    public function section()
    {
        return $this->belongsTo(KbliSection::class, 'section_id', 'id');
    }

    // Direct access to category through section
    public function category()
    {
        return $this->hasOneThrough(
            KbliCategory::class,
            KbliSection::class,
            'id', // Foreign key on kbli_sections table
            'id', // Foreign key on kbli_categories table
            'section_id', // Local key on kbli_items table
            'category_id' // Local key on kbli_sections table
        );
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'kbli_item_id');
    }
}
