<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hashtype;


class HashtypesController extends Controller
{
      public function list()
 {
    $hashtypes = Hashtype::all()->sortBy('id');;
    
    return view('hashtypes',['hashtypes' => $hashtypes]);
 }
 
 
 
       public function enable(Request $request,$id)
 {
    $hashtype = Hashtype::where('id',$id)->firstOrFail();
	$hashtype->enabled=FALSE;
	if ($request->filled('enabled')) {
    $hashtype->enabled=TRUE;
}
   
    $hashtype->save();

	
    return redirect('/hashtypes/');
 }

 
}
