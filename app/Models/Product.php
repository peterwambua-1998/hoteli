<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'price',
        'buying_price',
        'taxable',
    ];

    public function mainStore()
    {
        return $this->hasMany(MainStore::class, 'item_id');
    } 

    public function requisition()
    {
        return $this->hasMany(MaterialRequisitionItem::class, 'item_id');
    }

    public function recipe()
    {
        return $this->hasMany(Recipe::class, 'item_id');
    }
}
