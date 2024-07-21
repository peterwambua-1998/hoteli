<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequisitionItem extends Model
{
    use HasFactory;

    public function item()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }
}
