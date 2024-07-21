<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public function members() 
    {
        return $this->hasMany(AccountUser::class, 'account_id');
    }

    public function booking() 
    {
        return $this->hasMany(Booking::class);
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }
}
