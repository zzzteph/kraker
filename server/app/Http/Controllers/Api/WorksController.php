<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use App\Models\AgentInfo;
use App\Models\Inventory;
use App\Models\Hashtype;
use App\Models\Hashlist;
use App\Models\Template;
use App\Models\TemplateWordlist;
use App\Models\TemplateMask;
use App\Models\TemplateChain;
use App\Models\AgentInventory;
use App\Models\AgentStats;
use App\Models\AgentLogs;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\TaskChain;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class WorksController extends Controller
{



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


    public function get(Request $request)
    {

        try
        {
            $agent = Agent::where(['id' => $request->input('agent_id')])->firstOrFail();
            $agent->touch();

        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('access denied', 403);
        }
		

        //Hashlist task- If we add new hashlist, need its hashes count
        $hashlist=Hashlist::where('status','todo')->first();
        

        if ($hashlist !== null) { 
		
		        $content = Storage::get($hashlist->link);
               return response()->json(['type'=>'hashlist','hashlist_id'=> $hashlist->id,'hashtype_id'=>$hashlist->hashtype_id,"content"=>base64_encode($content)]);
        }
		
        
        //template task
        $templates=Template::where('status','todo')->get();
        foreach($templates as $template)
        {
               
               //mask can be send always 
               if($template->type==='mask')
               {
               
                 return response()->json([
                 'type'=>'templatebruteforce',
                 'template_id'=> $template->id,
                 'mask'=>$template->content->mask,
                 'charset1'=>$template->content->charset1,
                 'charset2'=>$template->content->charset2,
                 'charset3'=>$template->content->charset3,
                 'charset4'=>$template->content->charset4
                 ]);
               }
               
               //wordlist can be send only if agent has same inventory
               if($template->type==='wordlist')
               {
                   //check if wordlist and rule exists in agent inventory
                    if(!$this->canAgentExecuteTemplateWordlist($agent->id,$template->content->wordlist_id,$template->content->rule_id))continue;
                    return response()->json(['type'=>'templatewordlist','template_id'=> $template->id,'wordlist_id'=>$template->content->wordlist_id,'rule_id'=>$template->content->rule_id]);
 
               }         
        }
		
        
        //ordinary task
        //get all tasks
         $tasks=Task::where('status','todo')->orderBy('priority','desc')->get();
         //filter by tasks agent can execute
         $validTasks=collect();
         foreach($tasks as $task)
		 {
			
			//check that agent can execute template and has speed stats for that		
			$speedStats=AgentStats::where(['agent_id'=>$agent->id,'hashtype_id'=>$task->hashlist->hashtype_id])->first();
            //Ok, we can execute this template, lets do something else
            if( $speedStats===null)
            {
                continue;
            }
			   //Ok, we can execute something else if speed=0
			 if($speedStats->speed==0)
			 {
				 continue;
			 }
			
              if($task->current_chain->template->type=="mask")$validTasks->push($task);
			       
              if($task->current_chain->template->type=="wordlist")
              {
                    if($this->canAgentExecuteTemplateWordlist($agent->id,$task->current_chain->template->content->inventory_id,$task->current_chain->template->content->rule_id)==TRUE)
                    {
                      $validTasks->push($task);
                    }
              }   

         }
          //get all priorities to pick right one
        $priority=0;
        foreach( $validTasks as $task)
        {
            $priority+=$task->priority;
        }
	    $priority=rand(0,$priority);
        foreach( $validTasks as $task)
        {
            $priority=$priority-$task->priority;
            //we got right task
            if($priority<=0)
            {
              $job=Job::where(['status'=>'todo','task_chain_id'=>$task->current_chain->id])->orderBy('id')->first();
              if($job===null)continue;
               $job->status="running";
               $job->agent_id=$agent->id;
			  
               $job->save();
			   $hashlist=Hashlist::where('id',$task->hashlist_id)->first();
              if($task->current_chain->template->type=="mask")
              {
				  
				  
				  
                 return response()->json([
                 'type'=>'bruteforce',
                 'hashtype_id'=> $hashlist->hashtype_id,
                 'job_id'=> $job->id,
	             'skip'=> $job->skip,
                 'limit'=> $job->limit,
                 'mask'=>$task->current_chain->template->content->mask,
                 'charset1'=>$task->current_chain->template->content->charset1,
                 'charset2'=>$task->current_chain->template->content->charset2,
                 'charset3'=>$task->current_chain->template->content->charset3,
                 'charset4'=>$task->current_chain->template->content->charset4,
				 'content'=>base64_encode(Storage::get($hashlist->link)),
				 'pot_content'=>base64_encode($hashlist->pot)
                 ]); 
              }
              if($task->current_chain->template->type=="wordlist")
              {
                 

                   return response()->json([
                     'type'=>'wordlist',
                     'hashtype_id'=> $hashlist->hashtype_id,
                     'job_id'=> $job->id,
						'skip'=> $job->skip,
					'limit'=> $job->limit,
                     'wordlist_id'=>$task->current_chain->template->content->wordlist_id,
                     'rule_id'=>$task->current_chain->template->content->rule_id,
					 'content'=>base64_encode(Storage::get($hashlist->link)),
					 'pot_content'=>base64_encode($hashlist->pot)
                   ]); 
                   

                   
              }
              
              
              
              
              
            
            }
        
        }
		
		
		        //Speed Task - task for recalculating speeds each 6 hours
        $enabledHashtypes = Hashtype::where('enabled', 1)->get();
        foreach( $enabledHashtypes as $enabledHashtype)
        {
            $speedStats=AgentStats::where(['agent_id'=>$agent->id,'hashtype_id'=>$enabledHashtype->id])->first();
            //new speed stat or very old, 6 hours, need recalc
            if( $speedStats===null)
            {
                 return response()->json(['type'=>'speedstat','hashtype_id'=> $enabledHashtype->id]);
            }
			$speedStats=AgentStats::where(['agent_id'=>$agent->id,'hashtype_id'=>$enabledHashtype->id])->where('updated_at', '<', Carbon::now()->subHours(12)->toDateTimeString())->first();
			if( $speedStats!==null)
            {
                 return response()->json(['type'=>'speedstat','hashtype_id'=> $enabledHashtype->id]);
            }
        }
         
		
        
        	  $log=new AgentLogs;
			  $log->agent_id=$agent->id;
			  $log->info="idle";
			  $log->save();
        return response()->json(['type'=>'donothing']);
        
        
        
    }

  

}
