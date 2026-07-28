<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KbliCategory extends Model
{
    use HasFactory;

    protected $table = 'kbli_categories';

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    public function sections()
    {
        return $this->hasMany(KbliSection::class, 'category_id', 'id');
    }
}
