<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class project extends Model
{
       protected $fillable = ['name', 'db_file_path', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
