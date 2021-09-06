<?php

namespace App\Http\Controllers\Web;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



use App\Models\Inventory;
use App\Models\Hashtype;
use App\Models\Hashlist;
use App\Models\Template;
use App\Models\TemplateWordlist;
use App\Models\TemplateMask;
use App\Models\AgentInventory;
use App\Models\Task;
use App\Models\Job;
use App\Models\TemplateChain;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplatesController extends Controller
{
     

  public function new()
 {
   return view('templates.new',['templates'=>Template::where('status', 'valid')->where('type','!=', 'chain')->get(),'wordlists'=>Inventory::where('type', 'wordlist')->get(),'rules'=>Inventory::where('type', 'rule')->get()]);
 }
 
 public function create(Request $request)
 {

    if (!$request->filled('name')) {
     
        return redirect('/templates/new')->withErrors(['message' => 'You forget to set name']);
    }
    
        if (!$request->filled('TemplateType')) {
     
                return redirect('/templates/new')->withErrors(['message' => 'Template is not set']);
    }
    
    if($request->input('TemplateType')!=='wordlist' && $request->input('TemplateType')!=='mask' && $request->input('TemplateType')!=='chain')
    {
      return redirect('/templates/new')->withErrors(['message' => 'Unknown template type']);
    } 
    
	    if($request->input('TemplateType')=='mask' && !$request->filled('mask'))
    {
      return redirect('/templates/new')->withErrors(['message' => 'Mask cant be null']);
    } 
	
		    if($request->input('TemplateType')=='wordlist' && !$request->filled('wordlist'))
    {
      return redirect('/templates/new')->withErrors(['message' => 'Wordlist cant be null']);
    } 
	
	if($request->input('TemplateType')=='chain')
    {
		foreach($request->input('template') as $template)
		{
			$tmp=Template::where('id',$template)->firstOrFail();
			if($tmp->type=="chain")
				  return redirect('/templates/new')->withErrors(['message' => 'Loop chain detected']);
			  if($tmp->status!="valid")
				   return redirect('/templates/new')->withErrors(['message' => 'All templates must have valid status']);
		}
    } 
	
	
     if($request->input('TemplateType')=='wordlist')
     {

         Inventory::where('id',$request->input('wordlist'))->where('type','wordlist')->firstOrFail();
         
         if($request->has('rule'))Inventory::where('id',$request->input('rule'))->where('type','rule')->firstOrFail();
         $template=new Template();
         $template->type='wordlist';
         $template->name=$request->input('name');
         $template->status='todo';
         $template->save();
         $content=new TemplateWordlist();
         $content->template_id= $template->id;
         $content->wordlist_id=$request->input('wordlist');
         if($request->has('rule'))$content->rule_id=$request->input('rule');
         $content->save();   

     }
    
        
     if($request->input('TemplateType')=='mask')
     {
         $template=new Template();
         $template->type='mask';
         $template->name=$request->input('name');
         $template->status='todo';
         $template->save();
         $content=new TemplateMask();
         $content->template_id= $template->id;
         $content->mask=$request->input('mask');
         if($request->has('charset1'))$content->charset1=$request->input('charset1');
         if($request->has('charset2'))$content->charset2=$request->input('charset2');
         if($request->has('charset3'))$content->charset3=$request->input('charset3');
         if($request->has('charset4'))$content->charset4=$request->input('charset4');
         $content->save();   

     }
    
         if($request->input('TemplateType')=='chain')
     {
         $template=new Template();
         $template->type='chain';
         $template->name=$request->input('name');
         $template->status='valid';
         $template->save();
		 
		 
		foreach($request->input('template') as $chain)
		{
			$tmp=Template::where('id',$chain)->firstOrFail();
			$content=new TemplateChain();
			$content->template_id= $template->id;
			$content->chain_id=$tmp->id;
			$template->keyspace+=$tmp->keyspace;
            $content->save();   
			  if($tmp->status!="valid")
			  {
				    $template->status='error';
					 $template->save();
					  return redirect('/templates/new')->withErrors(['message' => 'During creation, all templates must have valid status!!']);
					
			  }
		}
		  $template->save();
     }
	 
	 
	 
    return redirect('/templates/');
 }
 
   public function get($id)
 {
    $template = Template::where('id',$id)->firstOrFail();
    return view('templates.get',['template' => $template]);
 }
 
 
 public function list()
 {
     $templates = Template::all()->sortBy('id');;
    
    return view('templates.list',['templates' => $templates]);
 } 
 
  public function delete($id)
 {
    $template = Template::where('id',$id)->firstOrFail();
    TemplateWordlist::where('template_id', $id)->delete();
    TemplateMask::where('template_id', $id)->delete();
    Template::where('id',$id)->delete();
    //todo:
    //wipe all tasks
    //wipe all jobs
    return redirect('/templates');
 }
}
