<?php

namespace App\Http\Controllers\Web\Notifications;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Telegram;
class TelegramController extends Controller
{
	
	public function get()
	{
		return view('notifications.telegram',['telegram' => Telegram::take(1)->first()]);
	}

 	public function post(Request $request)
	{                                                                                                                                       
		if ($request->filled(['token','chat','enabled'])) {
			$tg=Telegram::take(1)->first();
			if($tg==null)
			{
				$tg=new Telegram;

			}
			
			$tg->chat_id=$request->input('chat');
			$tg->token=$request->input('token');
			$tg->enabled=0;
			if($request->input('enabled')=="true")
			{
				$tg->enabled=1;
			}
			$tg->save();
   
		}
		 return redirect()->intended('notifications/telegram');
	}
 
}
