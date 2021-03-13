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
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class WebController extends Controller
{
	
	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect('/');
	}
	
	public function authentificate(Request $request)
	{

        $credentials = $request->only('name', 'password');

        if (Auth::attempt($credentials)) {
           $request->session()->regenerate();

            return redirect()->intended('dashboard');
			
        }
        return back()->withErrors([
            'message' => 'The provided credentials do not match our records.',
        ]);

	}

public function dashboard()
{
	return view('dashboard');
}



   public function inventory()
 {
    $inventory = Inventory::all()->sortBy('id');
    return view('inventory',['inventory' => $inventory]);
 }
 
 
   public function deleteinventory($id)
 {
    $inv=Inventory::where('id',$id)->firstOrFail();
    $inv->delete();
    return redirect('/inventory');
 }
 
 
 
}
