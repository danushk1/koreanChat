<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'fingerprint_id',
        'date',
        'usage_count',
    ];

    protected $casts = [
        'date' => 'date',
        'usage_count' => 'integer',
    ];
}
