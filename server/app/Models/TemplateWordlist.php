<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateWordlist extends Model
{
protected $table = 'template_wordlist';
    use HasFactory;

    
    
    public function wordlist()
    {
        return $this->belongsTo(Inventory::class,'wordlist_id','id');
    }
    public function rule()
    {
        return $this->belongsTo(Inventory::class,'rule_id','id');
    }
    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
