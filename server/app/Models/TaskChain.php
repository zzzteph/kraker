<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskChain extends Model
{
    use HasFactory;
	
	public function template()
  {
	    return $this->belongsTo(Template::class);
  }
	public function task()
  {
	    return $this->belongsTo(Task::class);
  }
	
	
	
	
	public function jobs()
    {
        return $this->hasMany(Job::class);
    }
		public function getTimeLeftAttribute()
	{
		
		$done=0;
		$done_jobs=0;
		$avg_time=0;
		$all=0;
		foreach($this->jobs as $job)
		{
			$all++;
			if($job->status=='done' || $job->status=='error') $done++;
			if($job->status=='done')
			{
				$done_jobs++;
				$avg_time+=$job->spend_time;
			}
		}
		if($avg_time==0)return FALSE;
		$avg_time=$avg_time/$done_jobs;
		return round($avg_time*($all-$done));

    }
	 
	
	
	public function getProgressAttribute()
	{
		$done=0;
		$all=0;
		foreach($this->jobs as $job)
		{
			$all++;
			if($job->status=='done' || $job->status=='error') $done++;
		}
		return round(($done/$all)*100);
    }
	 
	
	public function getCrackedAttribute()
	{
		$count=0;

		foreach($this->jobs as $job)
		{
			$count+=$job->cracked;
		}
		return $count;
    }
	
	public function getAgentsAttribute()
	{
		$agents=array();

		foreach($this->jobs as $job)
		{

			if($job->status=='running')
			{
				if($job->agent_id!==null)
					if(!in_array($job->agent_id,$agents))
						array_push($agents,$job->agent_id);
			}
		}
		return $agents;
    }
	
}
