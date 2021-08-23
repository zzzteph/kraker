<?php

namespace App\Http\Controllers\Web;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

class HashlistsController extends Controller
{
     
 	public function new()
	{
		$hashtypes = Hashtype::where('enabled',TRUE)->get();
   return view('hashlists.new',['hashtypes' => $hashtypes]);
	}
 
 
 
 public function create(Request $request)
 {
 
 

   if (($request->hasFile('hashfile')||$request->filled('hashlist_text')) && $request->has(['name','hashtype'])) {
   if (($request->hasFile('hashfile') && !$request->file('hashfile')->isValid()) && !$request->filled('hashlist_text'))     return back()->withErrors(['message' => 'File upload failed']);
    
       
       $filename=(string) Str::uuid();
	   
	   if($request->hasFile('hashfile') &&$request->file('hashfile')->isValid())
	   {
			$path = $request->file('hashfile')->storeAs('hashlists', $filename);
	   }
       else
	   {
		  $path='hashlists/'.(string) Str::uuid();
		  Storage::disk('local')->put($path, $request->input('hashlist_text'));   
	   }		   
	   
	   
	   
	   $hashlist=new Hashlist;
	   $hashlist->id= $filename;
       $hashlist->hashtype_id=$request->input('hashtype');
       $hashlist->name=$request->input('name');
       $hashlist->count=0;
       $hashlist->link=$path ;
       $hashlist->status='todo';
       $hashlist->save();
        return redirect()->intended('hashlists/'.$filename);
    }
    else
    
     return back()->withErrors(['message' => 'Not all fields were set']);
 }
 
  public function get($id)
 {
    $hashlist = Hashlist::where('id',$id)->firstOrFail();

    return view('hashlists.get',['hashlist' => $hashlist]);
 }
 
 
 
   public function list()
 {
    $hashlists = Hashlist::all()->sortByDesc('created_at');;
    
    return view('hashlists.list',['hashlists' => $hashlists]);
 }
 
 
  public function source($id)
 {
    $hashlist = Hashlist::where('id',$id)->firstOrFail();

	return Storage::download($hashlist->link);

 }
  
  public function cracked($id)
 {
    $hashlist = Hashlist::where('id',$id)->firstOrFail();
return response($hashlist->pot)

            ->header('Content-Type', 'plain/txt')
            ->header('Content-Disposition', 'attachment; filename="cracked.txt"');

 }
 
 
 
 public function delete($id)
 {
    
	$hashlist = Hashlist::where('id',$id)->firstOrFail();
	$hashlist->delete();
    Task::where('hashlist_id',$id)->delete();
    return redirect()->intended('hashlists');
 }
 
}
