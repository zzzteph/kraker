<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    public function template()
    {
        return $this->belongsTo(Template::class);
    }
    public function hashlist()
    {
        return $this->belongsTo(Hashlist::class);
    }
	
		public function task_chains()
    {
        return $this->hasMany(TaskChain::class);
    }

	
	public function getCurrentChainAttribute()
    {
        return $this->hasMany(TaskChain::class)->where('status','todo')->first();
		
    }
	
	public function getNextChainAttribute()
    {
      $count=$this->hasMany(TaskChain::class)->count();
	  foreach($this->template->content as $chain)
	  {
		  if($count==0)return $chain->template;
		  $count--;
	  }
		return FALSE;	  
    }
	
	
		public function getEtaAttribute()
	{
		$eta=0;

		foreach($this->task_chains as $chain)
		{
			$eta+=$chain->time_left;
		}
		if($eta==0)return FALSE;
		return $eta;
    }
	
	
	public function getProgressAttribute()
	{
		return round(($this->hasMany(TaskChain::class)->where('status','done')->count()/$this->template->parts)*100);
    }
	 
	
		public function getChainTotalAttribute()
    {
        return $this->template->parts;
		
    }
	
	public function getJobsAttribute()
	{
	//	if($this->template->type!=='chain')return $this->task_chains()->first()->jobs;
	//	return $this->task_chains()->orderBy('id')->first()->jobs;
		$jobs=collect();

		foreach($this->task_chains as $chain)
		{
			
			foreach($chain->jobs as $job)
			{
				$jobs->push($job);
			}
		}
		$jobs->flatten();
		return $jobs;
	
	
	
	
    }
	
	
	
	public function getCrackedAttribute()
	{
		$count=0;

		foreach($this->task_chains as $chain)
		{
			$count+=$chain->cracked;
		}
		return $count;
    }
	
	
	public function getAgentsAttribute()
	{
		$agents=array();

		foreach($this->task_chains as $chain)
		{
			if(!empty($chain->agents))
			array_push($agents,$chain->agents);
		}
		return $agents;
    }
	
	
}
