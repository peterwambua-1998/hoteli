<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Booking extends Model
{
    use HasFactory;

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function account() 
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function booking_items() 
    {
        return $this->hasMany(BookingDetail::class, 'booking_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

   
}
