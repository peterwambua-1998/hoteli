<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public function purchaseOrder()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'supplier_id');
    }

    public function goodsReceivedNote()
    {
        return $this->hasMany(GoodReceiveNote::class,'supplier_id');
    }
}
