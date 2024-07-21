<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceStore extends Model
{
    use HasFactory;

    public function item()
    {
        return $this->belongsTo(Product::class, 'item_id');
    } 
}
