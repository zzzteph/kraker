<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\AgentStats;
use App\Models\Inventory;
use App\Models\Hashtype;
use App\Models\Hashlist;
use App\Models\Template;
use App\Models\Task;
use App\Models\Job;
use App\Models\Pot;
use App\Models\Cracked;
use App\Models\TemplateSpeedStat;
use Carbon\Carbon;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Notifications\PasswordsCracked;
use App\Notifications\TaskDone;
class JobsController extends Controller
{

    public function put(Request $req,$id)
    {
		$agent = Agent::where(['id' => $req->input('agent_id')])->firstOrFail();
		$job=Job::where('id',$id)->firstOrFail();
		if (!$req->has(['outfile','potfile','speed','error']))
		{
			return response()->json('not found', 404);
		}

		if($req->input('potfile')!==null)
		{
			$potfile=base64_decode($req->input('potfile'));
			$potfileEntries = explode(PHP_EOL, $potfile);
			foreach($potfileEntries as $entry)
			{
				if(strlen($entry)==0)continue;
				$pot=Pot::where('hashlist_id',$job->task->hashlist_id)->where('pot_data',$entry)->first();
				if($pot==null)
				{
					$pot=new Pot;
					$pot->pot_data=$entry;
					$pot->hashlist_id=$job->task->hashlist_id;
					$pot->save();

				}

			}
		}
		if($req->input('outfile')!==null)
		{
			$outfile=base64_decode($req->input('outfile'));
			$outFileEntries = explode(PHP_EOL, $outfile);
	
			foreach($outFileEntries as $entry)
			{
				if(strlen($entry)==0)continue;
				
				$cracked = Cracked::where('hashlist_id', $job->task->hashlist_id)->where('plain', $entry)->first();
				if($cracked==null)
				{
					$cracked=new Cracked;
					$cracked->plain=$entry;
					$cracked->hashlist_id=$job->task->hashlist_id;
					$cracked->save();
					$job->cracked=$job->cracked+1;
				}

			}

		}
		$job->status='done';
		if($req->input('error')!==null)
		{
			$job->errors=$job->errors+1;
			if($job->errors<5)
				$job->status='todo';
			else
				$job->status='error';
		}
		if($job->status=='done')
		{
			$job->spend_time=$req->input('time');
			if($job->cracked>0)
				$job->notify(new PasswordsCracked($job));
		}
		$job->agent_id=null;
		$job->save();
		//update tasks 
		$task=Task::where('id',$job->task->id)->first();
		if($task->progress==100)
		{
			
			if($task!==null)
			{
				$task->status='done';
				$job->notify(new TaskDone($job));
				$task->save();
				
			}
			
		}
		
		//
		//update actual speed
		if($req->input('speed')>0)
		{


			$templateStat=TemplateSpeedStat::where(['agent_id' => $agent->id,'hashtype_id' => $job->task->hashlist->hashtype_id,'template_id'=>$job->task->template_id])->first();
			if($templateStat==null)
			{
				$templateStat=new TemplateSpeedStat;
				$templateStat->agent_id=$agent->id;
				$templateStat->hashtype_id=$job->task->hashlist->hashtype_id;
				$templateStat->template_id=$job->task->template_id;
				
			}
			$templateStat->speed=$req->input('speed');
			$templateStat->save();
		}
		
		 
	

    }


	
	

}
