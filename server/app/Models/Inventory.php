<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
	protected $table = 'inventory';
	protected $fillable = ['name','size','checksum','count','type'];
    use HasFactory;

	public function agents()
    {
        return $this->hasMany(AgentInventory::class);
    }
 protected static function boot() {
    parent::boot();
        static::deleting(function ($inventory) {

			$templates=TemplateWordlist::where('wordlist_id',$inventory->id)->orWhere('rule_id',$inventory->id)->get();
			foreach($templates as $template)
			{
				Template::where('id',$template->template_id)->delete();	
			}
			AgentInventory::where('inventory_id',$inventory->id)->delete();

        });
    }
}
