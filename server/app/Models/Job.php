<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class Job extends Model
{
    use HasFactory;
	use Notifiable;
	    public function task_chain()
    {
        return $this->belongsTo(TaskChain::class,'task_chain_id','id');
    }
	
		public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
