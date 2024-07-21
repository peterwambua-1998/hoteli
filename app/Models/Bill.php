<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(BillItem::class, 'bill_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function debitNote()
    {
        return $this->hasMany(BillDebitNote::class, 'bill_id');
    }

    public function creditNote()
    {
        return $this->hasMany(BillCreditNote::class, 'bill_id');
    }

    public function receipt()
    {
        return $this->hasMany(BillReceipt::class, 'bill_id');
    }
}
