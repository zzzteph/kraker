<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentStats extends Model
{
	
protected $keyType = 'string';
	protected $table = 'agent_stats';
	protected $guarded = [];
	public $incrementing = false;
    use HasFactory;
}
