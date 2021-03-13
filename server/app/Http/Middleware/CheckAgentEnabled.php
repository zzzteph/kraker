<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use App\Models\Agent;
use App\Channels\TelegramChannel;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAgent;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class CheckAgentEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
		
		if(Auth::check())
		{
			return $next($request);
		}
		
        try
        {
         $agent = Agent::where(['id' => $request->input('agent_id')])->firstOrFail();
		
$agent->touch();
		 
		 if($agent->enabled!==1)
		      return response()->json('access denied', 403);
		 }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('unauthorized', 401);
        }
        
          
        

        return $next($request);
    

    }
}
