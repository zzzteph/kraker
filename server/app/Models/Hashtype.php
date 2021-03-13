<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtype extends Model
{
    use HasFactory;
    
 
 
 public function getAvgSpeedAttribute()
 {
     $agents=Agent::where('enabled','1')->get();
     $speed=0;
     $availableAgents=0;
     foreach($agents as $agent)
     {
          $stats=AgentStats::where('agent_id',$agent->id)->where('hashtype_id',$this->id)->first();
          if( $stats!==null)
          {
            $speed+=$stats->speed;
            $availableAgents++;
          }
     }
     if($availableAgents==0)return 0;
     return round($speed/$availableAgents);
    
 }
 
 
    
}
