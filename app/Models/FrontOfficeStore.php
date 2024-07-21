<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FrontOfficeStore extends Model
{
    use HasFactory;

    
    public function item()
    {
        return $this->belongsTo(Product::class, 'item_id');
    } 

    
}
