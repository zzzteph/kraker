<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Template;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TemplatesController extends Controller
{
    public function list()
    {
        return response()->json(Template::all());
    }
    
        public function get($id)
    {
                try
        {  
 
  
            return response()
                ->json(Template::where(['id' => $id])->firstOrFail());
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
            $template = Template::where(['id' => $id])->firstOrFail();
            if ($req->has('keyspace'))
            {
               
               $template->keyspace=$req->input('keyspace');
			   if($req->input('keyspace')==0)
				$template->status='error';
				else
				$template->status='valid';
               $template->save();
            }

        }
        catch(ModelNotFoundException $ex)
        {
            return response()->json('not found', 404);
        }
    }
	
    
}
