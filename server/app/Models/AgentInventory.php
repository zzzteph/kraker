<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentInventory extends Model
{
	protected $keyType = 'string';
	protected $table = 'agent_inventory';
	protected $guarded = [];
    use HasFactory;

	
	public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
	
	
}
