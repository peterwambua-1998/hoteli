<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillReceipt extends Model
{
    use HasFactory;

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function refund()
    {
        return $this->hasMany(BillReceiptRefund::class, 'bill_receipt_id');
    }
}
