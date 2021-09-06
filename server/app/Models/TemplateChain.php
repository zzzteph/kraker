<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateChain extends Model
{
    use HasFactory;
	
	
	  public function template()
  {
	    return $this->belongsTo(Template::class,'chain_id', 'id');
  }
  
	
}
