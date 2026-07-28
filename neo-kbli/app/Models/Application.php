<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'applications';

    protected $fillable = [
        'tracking_code',
        'kbli_item_id',
        'user_id',

        'product_name',
        'product_material',
        'production_process',
        'product_description',
        'saker_address',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'status' => \App\Enums\ApplicationStatus::class,
    ];

    public function kbliItem()
    {
        return $this->belongsTo(KbliItem::class, 'kbli_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }



    public function histories()
    {
        return $this->hasMany(ApplicationHistory::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
