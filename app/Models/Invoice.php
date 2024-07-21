<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function receipt()
    {
        return $this->hasMany(Receipt::class, 'invoice_id');
    }

    public function debitNote()
    {
        return $this->hasMany(DebitNote::class, 'invoice_id');
    }

    public function creditNote()
    {
        return $this->hasMany(CreditNote::class, 'invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
