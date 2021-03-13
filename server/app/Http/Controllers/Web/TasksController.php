<?php

namespace App\Http\Controllers\Web;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TasksController extends Controller
{
     

  public function new()
 {
   return view('tasks.new');
 }
   public function list()
 {
   return view('tasks.list',['tasks' => Task::paginate(10)]);
 }
    public function get($id)
 {
    return view('tasks.get',['task' => Task::where('id',$id)->firstOrFail()]);
 }
 
    public function start($id)
 {
   $task=Task::where('id',$id)->where('status','stopped')->firstOrFail();
   $task->status="todo";
   $task->save();
   return redirect()->intended('tasks');
 }
 
     public function stop($id)
 {
      $task=Task::where('id',$id)->where('status','todo')->firstOrFail();
   $task->status="stopped";
   $task->save();
   return redirect()->intended('tasks');
 }
}
