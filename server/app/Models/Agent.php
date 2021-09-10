<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use Illuminate\Notifications\Notifiable;
class Agent extends Model
{
	use Notifiable;
	protected $keyType = 'string';
	use HasFactory;
	protected $guarded = [];
	
 
 private function get_info_by_key($key)
 {

    foreach($this->info as $infoEntry)
    {
      if($infoEntry->key==$key)return $infoEntry->value;
    }
	
	 
 }
 
 
 
 
 public function getLastSeenAttribute()
 {
 
    return $this->updated_at->diffInSeconds();
 }
 
 
	public function getIpAttribute()
	{ 
    return $this->get_info_by_key('ip');
	}
	 
   	public function getHostnameAttribute()
	{ 
    return $this->get_info_by_key('hostname');
	}
 	public function getOsAttribute()
	{ 
    return $this->get_info_by_key('os');
	}
 	public function  getHashcatAttribute()
	{ 
    return $this->get_info_by_key('hashcat');
	}
	  
    public function  getHwAttribute()
	{ 
		return $this->get_info_by_key('hw');
	}
	  
     
	 	public function job()
    {
        return $this->hasMany(Job::class);
    }
	
	 
	public function info()
    {
        return $this->hasMany(AgentInfo::class);
    }
	
	public function getLatestActionAttribute()
	{
			$agentLog=$this->hasMany(AgentLogs::class)->orderBy('id','desc')->first();
			if($agentLog==null)return "";
			return $agentLog->info;
			//parsing agentLog
			
			
			
	}
	
	public function logs()
    {
        return $this->hasMany(AgentLogs::class)->orderBy('id');;
    }
	
	public function agent_inventory()
	{
		 return AgentInventory::where('agent_id', $this->id)->get();
	}
	
 	public function speed_stats()
	{
        return $this->hasMany(AgentStats::class);
  }
	
 
 
	public function getInventoryAttribute()
	{
		$inventory=collect();
		foreach($this->agent_inventory() as $invItem)
		{
			 $inventory->push(Inventory::where('id',$invItem->inventory_id)->first());
		}
		return $inventory;
    }
	
	
}
