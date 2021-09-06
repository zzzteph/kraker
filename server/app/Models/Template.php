<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;
   
    public function getPartsAttribute()
	{
		if($this->type!="chain")return 1;
		return $this->hasMany(TemplateChain::class)->count();
	}
    
   public function content()
	{
       if($this->type=="mask")
          return $this->hasOne(TemplateMask::class);
        
        else   if($this->type=="wordlist")
          return $this->hasOne(TemplateWordlist::class);
	  else
		   return $this->hasMany(TemplateChain::class);
  }
  
  public function tasks()
  {
	    return $this->hasMany(Task::class);
  }
  
  

}
