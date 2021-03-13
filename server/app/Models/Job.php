<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Job extends Model
{
    use HasFactory;
	use Notifiable;
	    public function task()
    {
        return $this->belongsTo(Task::class,'task_id','id');
    }
	
	
}
