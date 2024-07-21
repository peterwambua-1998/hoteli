<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    public function proforma()
    {
        return $this->hasMany(ProformaInvoice::class, 'bank_account_id');
    }

    public function quotation()
    {
        return $this->hasMany(Quotation::class, 'bank_account_id');
    }

    public function receipt()
    {
        return $this->hasMany(Receipt::class, 'bank_account_id');
    }

    public function billReceipt()
    {
        return $this->hasMany(BillReceipt::class, 'bank_account_id');
    }
}
