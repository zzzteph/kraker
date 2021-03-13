<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pot extends Model
{
 protected $table = 'pot';
 protected $fillable = ['pot_data', 'hashlist_id'];
    use HasFactory;
}
