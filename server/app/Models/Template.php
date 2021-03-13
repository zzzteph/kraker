<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
   
    
    
   public function content()
	{
       if($this->type=="mask")
          return $this->hasOne(TemplateMask::class);
        
        else
          return $this->hasOne(TemplateWordlist::class);
  }
  
  public function tasks()
  {
	    return $this->hasMany(Task::class);
  }
  
  

}
