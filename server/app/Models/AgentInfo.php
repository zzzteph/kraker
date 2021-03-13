<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentInfo extends Model
{
	protected $keyType = 'string';
	protected $table = 'agent_info';
	protected $fillable = ['agent_id','key','value'];
    use HasFactory;
}
