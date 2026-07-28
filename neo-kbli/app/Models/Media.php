<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    const UPDATED_AT = null;

    protected $fillable = [
        'application_id',
        'path',
        'stored_name',
        'original_name',
        'mime_type',
        'size',
        'checksum',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
