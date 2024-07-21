<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class,'purchase_order_id');
    }

    public function notes()
    {
        return $this->hasMany(GoodReceiveNote::class,'purchase_order_id');
    }
}
