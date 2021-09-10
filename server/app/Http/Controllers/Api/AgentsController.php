<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Models\AgentInfo;
use App\Models\Inventory;
use App\Models\Hashtype;
use App\Models\AgentInventory;
use App\Models\AgentStats;
use App\Models\AgentLogs;
use App\Models\Job;
use App\Models\ForceTask;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Notifications\NewAgent;
use App\Notifications\AgentOnline;

class AgentsController extends Controller
{

    public function register()
    {
        $id = (string)Str::uuid();
        Agent::create(['id' => $id, 'enabled' => 0]);
		$agent = Agent::where(['id' => $id])->firstOrFail();

		$agent->notify(new NewAgent($agent));
		
		
        return response()->json(['id' => $agent->id]);

    }


		public function checkRegistration($id)
		{
			
			try
			{
				$agent = Agent::where(['id' => $id])->firstOrFail();
				$agent->notify(new AgentOnline($agent));
				return response()
					->json($agent);
			}
			catch(ModelNotFoundException $ex)
			{
				return response()->json('unauthorized', 401);
			}
		}


    public function heartbeat(Request $req,$id)
    {
		$agent = Agent::where(['id' => $id])->firstOrFail();
		$agent->touch();
		//cancel to stop current task
		  if ($req->has(['type','id']))
		  {
			  $log=new AgentLogs;
			  $log->agent_id=$id;
			  $log->info=$req->input('type').":".$req->input('id');
			  $log->save();
			  if($req->input('type')=="wordlist" || $req->input('type')=="bruteforce")
			  {
				  $job=Job::where("id",$req->input('id'))->first();
				  if($job!==null)$job->touch();
			  }
			  
		  }
		
		$force=ForceTask::where('agent_id',$id)->where('status','todo')->first();
		if($force!=null)
		{
			
			$force->status='done';
			$force->save();
			return response()->json(['status'=>$force->action]);
		}
		
		
		return response()->json(['status'=>'continue']);
    }

    public function get($id)
    {

        try
        {
            $agent = Agent::where(['id' => $id])->firstOrFail();
            return response()
                ->json($agent);
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }

    public function list()
    {
        $agents = Agent::where('updated_at', '>', Carbon::now()->subHours(6)
            ->toDateTimeString())
            ->get();
        return response()
            ->json($agents);
    }

    public function updateInfo(Request $req, $id)
    {
        try
        {
            $agent = Agent::where(['id' => $id])->firstOrFail();
            
            if ($req->has('ip')) $agent->info()
                ->updateOrCreate(['key' => 'ip'], ['value' => $req->input('ip') ]);
            if ($req->has('hostname')) $agent->info()
                ->updateOrCreate(['key' => 'hostname'], ['value' => $req->input('hostname') ]);
            if ($req->has('os')) $agent->info()
                ->updateOrCreate(['key' => 'os'], ['value' => $req->input('os') ]);
            if ($req->has('hashcat')) $agent->info()
                ->updateOrCreate(['key' => 'hashcat'], ['value' => $req->input('hashcat') ]);
            if ($req->has('hw')) $agent->info()
                ->updateOrCreate(['key' => 'hw'], ['value' => $req->input('hw') ]);
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }

    public function getInfo($id)
    {
        try
        {
            $agent = Agent::where(['id' => $id])->firstOrFail();

            return response()
                ->json($agent->info);
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }

    public function updateInventory(Request $req, $id)
    {
        try
        {
            $agent = Agent::where(['id' => $id])->firstOrFail();

            //truncating inventory
            AgentInventory::where('agent_id', $agent->id)->delete();
            foreach ($req->input() as $invEntry)
            {
                if (isset($invEntry['name']) && isset($invEntry['size']) && isset($invEntry['count']) && isset($invEntry['checksum']) && isset($invEntry['type']))
                {
					try { 
						$inventory = Inventory::where('name', $invEntry['name'])->where('size' ,$invEntry['size'])->where('count',$invEntry['count'])->where('type',$invEntry['type'])->where('checksum',$invEntry['checksum'])->first();
						if($inventory===null)
						{
							$inventory=new Inventory;
							$inventory->name=$invEntry['name'];
							$inventory->size=$invEntry['size'];
							$inventory->count=$invEntry['count'];
							$inventory->type=$invEntry['type'];
							$inventory->checksum=$invEntry['checksum'];
							$inventory->save();
							
						}
					
					
					if (AgentInventory::where('agent_id', $agent->id)->where('inventory_id',$inventory->id)->first() === null) {
					
						
							$agent_inv_entry = new AgentInventory;
							$agent_inv_entry->agent_id = $agent->id;
							$agent_inv_entry->inventory_id = $inventory->id;
							$agent_inv_entry->save();
					


					}
						} catch(\Illuminate\Database\QueryException $ex){ 
						 
						}
                }
            }
			
			 return response()->json($agent->inventory);
		
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }

    public function listInventory($id)
    {
        try
        {
            $agent = Agent::where(['id' => $id])->firstOrFail();

            return response()
                ->json($agent->inventory);
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }

    public function getSpeedStats(Request $req, $id)
    {
        try
        {
            $agent = Agent::where(['id' => $id])->firstOrFail();

            return response()
                ->json($agent->speed_stats);
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }

    public function updateSpeedStat(Request $req, $id)
    {

        try
        {
            $agent = Agent::where(['id' => $id])->firstOrFail();
			Hashtype::where(['id' => $req->input('hashtype_id')])->firstOrFail();
			//hack cause no support for composite keys
			//need to refactor somehow
			AgentStats::where(['agent_id' => $agent->id,'hashtype_id' => $req->input('hashtype_id')])->delete();

			$stats=new AgentStats;
			$stats->agent_id=$agent->id;
			$stats->hashtype_id=$req->input('hashtype_id');
			$stats->speed=$req->input('speed');
			$stats->save();


        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }



    }

}
