<?php

namespace App\Http\Controllers\Web;
use Illuminate\Support\Facades\Auth;
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
use App\Models\AgentInventory;
use App\Models\Task;
use App\Models\ForceTask;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgentsController extends Controller
{
 	public function list()
	{
		$agents = Agent::where('updated_at','>', Carbon::now()->subHours(6)->toDateTimeString())->get();
		return view('agents.list',['agents' => $agents]);
	}

	public function get($id)
	{
		return view('agents.get',['agent' => Agent::where('id',$id)->firstOrFail()]);
	}
 
 
	  public function delete($id)
	 {
		 $agent = Agent::where('id',$id)->firstOrFail();
		 Agent::where('id', $id)->delete();
		 return redirect()->intended('agents');
	 }
	 
 	  public function reset($id)
	 {
		 $agent = Agent::where('id',$id)->firstOrFail();
		$force=new ForceTask;
		$force->agent_id= $agent->id;
		$force->action='stop';
		$force->save();
		 return redirect()->intended('agents');
	 }
	 
 
 
   public function update(Request $request,$id)
 {
     	$agent = Agent::where('id',$id)->firstOrFail();
      if ($request->has('enabled'))
      {
        	$agent->enabled=$request->input('enabled');
         	$agent->save();
      }
      return redirect()->intended('agents');
 }
 
 
}
