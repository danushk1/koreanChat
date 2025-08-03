<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fingerprint_id',
        'start_date',
        'end_date',
        'is_active',
        'token_balance',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Optional: relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional: check if subscription is active
    public function isCurrentlyActive(): bool
    {
        return $this->is_active && $this->end_date->isFuture();
    }
}
