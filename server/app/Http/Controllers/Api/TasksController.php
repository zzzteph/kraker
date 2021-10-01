<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\AgentInfo;
use App\Models\Inventory;
use App\Models\Hashtype;
use App\Models\Hashlist;
use App\Models\Template;
use App\Models\Task;
use App\Models\TaskChain;
use App\Models\Job;
use App\Models\AgentInventory;
use App\Models\AgentStats;
use App\Models\TemplateSpeedStat;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class TasksController extends Controller
{
	
	
	public function status(Request $req,$id)
	{
		$task=Task::where('id',$id)->firstOrFail();
		
		if($req->filled('status'))
		{
			if($req->input('status')=='stopped')
			{
				$task->status=$req->input('status');
				$task->save();
			}
			if($req->input('status')=='todo')
			{
				$task->status=$req->input('status');
				$task->save();
			}
			
		}
		
	}
	
	
	public function priority(Request $req,$id)
	{
		$task=Task::where('id',$id)->firstOrFail();
		if($req->filled('action'))
		{
			if($req->input('action')=='increase')
			{
				if($task->priority<100)
				$task->priority=$task->priority+1;
				$task->save();
				
			}
		
			if($req->input('action')=='decrease')
			{
				if($task->priority>0)
				$task->priority=$task->priority-1;
				$task->save();
				
			}
		}

	}

	
	
	public function live()
    {
		$response=collect();
		$tasks=Task::where('status','todo') ->orderBy('priority', 'desc')->get();
		foreach($tasks as $task)
		{
			$response->push(
			array(
			'id'=>$task->id,
			'priority'=>$task->priority,
			'status'=>$task->status,
			'hashlist_id'=>$task->hashlist_id,
			'template_id'=>$task->template_id,
			'template_name'=>$task->template->name,
			'hashlist_name'=>$task->hashlist->name,
			'hashtype_id'=>$task->hashlist->hashtype_id,
			'progress'=>$task->progress,
			'cracked'=>$task->cracked,
			'agents'=>$task->agents,
			'eta'=>$task->eta
			)
			);
		}
		
		
		
      return response()->json($response);

    }

	
	

    public function get()
    {
      

    }

   private function canAgentExecuteTemplateWordlist($agent_id,$wordlist_id,$rule_id)
    {
       if($this->checkIfAgentHasInventory($agent_id,$wordlist_id))
       {
                    if(!$this->checkIfAgentHasInventory($agent_id,$rule_id))return FALSE;
                    return TRUE;
        }
        return FALSE;
    }

    private function checkIfAgentHasInventory($agent_id,$inventory_id)
    {
    //empty inv
      if(is_null($inventory_id))return TRUE;
      
        if(AgentInventory::where('agent_id',$agent_id)->where('inventory_id',$inventory_id)->count()>0)return TRUE;
        return FALSE;
    }

	function calc_charset_size($charset)
	{
		$l = 26;
		$u = 26;
		$d = 10;
		$s = 41;
		$a = $l+$u+$d+$s;
		$b = 256;
		$size=0;
		
		//we can met charset only one time 
		$bool_l=false;
		$bool_u=false;
		$bool_d=false;
		$bool_s=false;
		$bool_a=false;
		$bool_b=false;
		
		
		
		
		for($i=0;$i<strlen($charset);$i++)
		{
			if($charset[$i]=='?' && ($i+1)<strlen($charset))
			{
				switch($charset[$i+1])
				{
					case "l": if(!$bool_l){$size+=$l;$i++;$bool_l=true;}break;
					case "u": if(!$bool_u){$size+=$u;$i++;$bool_u=true;}break;
					case "d": if(!$bool_d){$size+=$d;$i++;$bool_d=true;}break;
					case "s": if(!$bool_s){$size+=$s;$i++;$bool_s=true;}break;
					case "a": if(!$bool_a){$size+=$a;$i++;$bool_a=true;}break;
					case "b": if(!$bool_b){$size+=$b;$i++;$bool_b=true;}break;
					default:return NULL;
				}
			}
			else
			$size++;
		}
		return $size;
	}
	
	function keyspace_mask($mask,$charset_1,$charset_2,$charset_3,$charset_4)
	{
		
		/*
		  ? | Charset
 ===+=========
  l | abcdefghijklmnopqrstuvwxyz
  u | ABCDEFGHIJKLMNOPQRSTUVWXYZ
  d | 0123456789
  h | 0123456789abcdef
  H | 0123456789ABCDEF
  s |  !"#$%&'()*+,-./:;<=>?@[\]^_`{|}~
  a | ?l?u?d?s
  b | 0x00 - 0xff
  */
		
		
		
		
		
		$l = 26;
		$u = 26;
		$d = 10;
		$s = 33;
		$h=16;
		$a = $l+$u+$d+$s;
		$b = 256;
		
		
		$sizemul=-1;//size for charset iteration
		$singlesize=0;//single characters
		$totalsize=0;	
		$charset_1_size=$this->calc_charset_size($charset_1);
		$charset_2_size=$this->calc_charset_size($charset_2);
		$charset_3_size=$this->calc_charset_size($charset_3);
		$charset_4_size=$this->calc_charset_size($charset_4);
		
		
		if($charset_1!=null && $charset_1_size==null)return null;
		if($charset_2!=null && $charset_2_size==null)return null;
		if($charset_3!=null && $charset_3_size==null)return null;
		if($charset_4!=null && $charset_4_size==null)return null;
		for($i=0;$i<strlen($mask);$i++)
		{
			if($mask[$i]=='?' && ($i+1)<strlen($mask))
			{
				if($sizemul==-1)
				 $sizemul=1;
				switch($mask[$i+1])
				{
					case "l":$sizemul*=$l;$i++;break;
					case "u":$sizemul*=$u;$i++;break;
					case "d":$sizemul*=$d;$i++;break;
					case "s":$sizemul*=$s;$i++;break;
					case "a":$sizemul*=$a;$i++;break;
					case "b":$sizemul*=$b;$i++;break;
					case "h":$sizemul*=$h;$i++;break;
					case "H":$sizemul*=$h;$i++;break;
					case "1":$sizemul*=$charset_1_size;$i++;break;
					case "2":$sizemul*=$charset_2_size;$i++;break;
					case "3":$sizemul*=$charset_3_size;$i++;break;
					case "4":$sizemul*=$charset_4_size;$i++;break;
					default:return NULL;
				}
			}
			else if($mask[$i]!='?' && $sizemul<=1 && $singlesize==0)
				$singlesize=1;	
		}
	
		if($sizemul==0)
			return NULL;
		if($sizemul>0)
			$totalsize=$sizemul;
		else
			$totalsize=$singlesize;
		return $this->check_keyspace($totalsize);
	}
	


	function check_keyspace($keyspace)
	{
		if($keyspace>1000000000000000000|| $keyspace<=0)return NULL;
		return $keyspace;
	}
	
	
	function calculate_real_keyspace($template_id,$hashlist_id)
	{
		$hashlist=Hashlist::where('id',$hashlist_id)->firstOrFail();
		$template=Template::where('id',$template_id)->firstOrFail();
				//salted hash?
		$hashtype=Hashtype::where('id',$hashlist->hashtype_id)->firstOrFail ();
		
		$salted=$hashtype->salted;
		//
		$totalKeyspace=0;

		if($template->type=='mask')
		{
			$totalKeyspace=$this->keyspace_mask($template->content->mask,$template->content->charset1,$template->content->charset2,$template->content->charset3,$template->content->charset4);
			
			if($salted==1)
			{
				$totalKeyspace=$totalKeyspace*$hashlist->count;
			}
		}
		
		if($template->type=='wordlist')
		{
			
			$wordlist=Inventory::where('id',$template->content->wordlist_id)->firstOrFail();
			$rule=null;
			if($template->content->rule_id!==null)
				$rule=Inventory::where('id',$template->content->rule_id)->firstOrFail();
			
			
			$totalKeyspace=$template->keyspace;
			if($rule!==null)
			{
				if($rule->count!=0)
				$totalKeyspace=$template->keyspace*$rule->count;
			}
			if($salted==1)
			{
				$totalKeyspace=$totalKeyspace*$hashlist->count;
			}
		}
		return $totalKeyspace;
		
		
	}
	
	
	function calculate_avg_agents_speed($template_id,$hashlist_id)
	{
			$hashlist=Hashlist::where('id',$hashlist_id)->firstOrFail();
			$template=Template::where('id',$template_id)->firstOrFail();
					$agents = Agent::where('enabled',1)->get();
		$totalSpeed=0;
		$validAgents=0;
		foreach($agents as $agent)
		{
			foreach($agent->speed_stats as $agent_speed)
			{
				if($agent_speed->hashtype_id==$hashlist->hashtype_id)
				{
					if($template->type=='wordlist')
					{
						  if(!$this->canAgentExecuteTemplateWordlist($agent->id,$template->content->wordlist_id,$template->content->rule_id))continue;
					}
					//check if speed for template and hashlist exists
				$templateStats=TemplateSpeedStat::where(['agent_id' => $agent->id,'hashtype_id' => $hashlist->hashtype_id,'template_id'=>$template->id])->first();
				if($templateStats!=null)
				{
					$totalSpeed+=$templateStats->speed;
				}
				else{
					$totalSpeed+=$agent_speed->speed;
				}
					
					
					$validAgents++;
				}
			}
		}
		if($validAgents==0)return FALSE;
		return round($totalSpeed/$validAgents);
			
			
	}
	
	function pretty_time($secs)
	{
		$mins =  $secs/60;
		$hrs  = $mins/60;
		$days = $hrs/24;
		$months = $days/30.5;
		$years = $days/365;

		if ( $years> 1 )
		{
			return ("~ ".round($years,1)." years");

		}
		if ( $months> 1 )
		{
			return ("~ ".round($months,1)." months");

		}
		if ( $days> 1 )
		{
			return ("~ ".round($days,1)." days");

		}
		if ( $hrs> 1 )
		{
			return ("~ ".round($hrs,1)." hours");

		}
		if ( $mins> 1 )
		{
			return ("~ ".round($mins,1)." minutes");

		}
			return ("~ ".round($secs,1)." seconds");
	}
 
 
 
	public function calculate(Request $req)
	{
		if (!$req->filled(['hashlist_id','template_id']))  return response()->json(0);

		
	
		$totalKeyspace=$this->calculate_real_keyspace($req->input('template_id'),$req->input('hashlist_id'));
		$avgSpeed=$this->calculate_avg_agents_speed($req->input('template_id'),$req->input('hashlist_id'));

		if($avgSpeed==FALSE || $avgSpeed==0)
			return response()->json(FALSE);
		
		
		
		

		return response()->json($this->pretty_time($totalKeyspace/$avgSpeed));
	}
	
	public function parts_calculation($template_id,$hashlist_id)
	{
		
		$hashlist=Hashlist::where('id',$hashlist_id)->firstOrFail();
		$template=Template::where('id',$template_id)->firstOrFail();
		
		$totalKeyspace=$this->calculate_real_keyspace($template_id,$hashlist_id);
		$avgSpeed=$this->calculate_avg_agents_speed($template_id,$hashlist_id);
		if($avgSpeed==FALSE || $avgSpeed==0)
			return FALSE;
		
		$diffKeyspace=round($totalKeyspace/$template->keyspace);
		if($diffKeyspace<=1)$diffKeyspace=1;

		//how many parts can be done in 10 minutes (600 secs)
		
		$parts=round((300*$avgSpeed)/$diffKeyspace);
		
		$parts=min($parts,$template->keyspace);
		return $parts;
	}
	
	
	
	
	public function create(Request $req)
	{
		
			if (!$req->filled(['hashlist_id','template_id']))  return response()->json(0);

	
		$hashlist=Hashlist::where('id',$req->input('hashlist_id'))->firstOrFail();
		$template=Template::where('id',$req->input('template_id'))->firstOrFail();
		$parts=$this->parts_calculation($req->input('template_id'),$req->input('hashlist_id'));
		if($parts===FALSE)
			return response()->json(FALSE);
		$task=new Task;
		$task->hashlist_id=$hashlist->id;
		$task->template_id=$template->id;
		$task->save();
		
		$taskChain=new TaskChain;
		$taskChain->task_id=$task->id;
		if($template->type!=='chain')
		{
			$taskChain->template_id=$task->template_id;
			$taskChain->save();
		}
		else
		{	//chain have little different logic
			$chain=$template->content()->first();
			$taskChain->template_id=$chain->chain_id;
			$taskChain->save();
			//calculate parts for chain
			$template=Template::where('id',$chain->chain_id)->firstOrFail();
			$parts=$this->parts_calculation($template->id,$hashlist->id);
		}
		
		$insertJob=array();
		for($i=0;$i<=$template->keyspace;$i+=$parts)
		{
			if($i>=$template->keyspace)break;
			array_push($insertJob,array('task_chain_id'=>$taskChain->id,'skip'=>$i,'limit'=>$parts));
		}			
		Job::insert($insertJob);
		return response()->json($task->id);
	
		
		
		

		
	}
	
	
	

}
