<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class user_ip extends Model
{
  use HasFactory;
     protected $fillable = ['device_ip', 'user_id','token'];
}
