<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationHistory extends Model
{
    use HasFactory;

    protected $table = 'application_histories';

    const UPDATED_AT = null;

    protected $fillable = [
        'application_id',
        'status',
                'notes',
        'created_by',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
