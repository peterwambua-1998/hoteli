<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    use HasFactory;

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_id');
    }
}
