<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class InventoryController extends Controller
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
    
    

    
    
    
    
}
