<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Hashlist extends Model
{
	protected $keyType = 'string';
    use HasFactory;
    use SoftDeletes;
    
  public function getHashtypeNameAttribute()
	{ 
    $hashtype=Hashtype::where('id',$this->hashtype_id)->first();
    return $hashtype->name;
	}
    
    public function getCrackedCountAttribute()
	{ 
      return $this->hasMany(Cracked::class)->count();
	}  
     public function getPotAttribute()
	{ 
		$result="";
     $potData=Pot::where('hashlist_id',$this->id)->get();
	foreach(  $potData as $entry)
	{
		$result.=$entry->pot_data.PHP_EOL;
	}
	 return $result;
	} 
	
	     public function getPotDataAttribute()
	{ 
		$result=array();
     $potData=Pot::where('hashlist_id',$this->id)->get();
	foreach(  $potData as $entry)
	{
		array_push($result,$entry->pot_data);
	}
	 return $result;
	}
	
	
}
