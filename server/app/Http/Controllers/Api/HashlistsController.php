<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Hashlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class HashlistsController extends Controller
{
    public function list()
    {
        return response()->json(Hashlist::all());
    }
    
        public function get($id)
    {
                try
        {
 
  
            return response()
                ->json(Hashlist::where(['id' => $id])->firstOrFail());
        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }
    
    
    public function download($id)
    {
                    try
        {
        $hashlist=Hashlist::where(['id' => $id])->firstOrFail();
        
        $contents = Storage::get($hashlist->link);
        return response()->json( ["content"=>base64_encode($contents)] );
                }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }
    
    
        public function updateInfo(Request $req, $id)
    {
        try
        {
            $hashlist = Hashlist::where(['id' => $id])->firstOrFail();
            if ($req->has('count'))
            {
               
               $hashlist->count=$req->input('count');
			   if($req->input('count')==0)
				$hashlist->status='error';
				else
				$hashlist->status='valid';
               $hashlist->save();
            }

        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }
    
    
    
    
}
