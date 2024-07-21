<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;


    public function items()
    {
        return $this->hasMany(PackageItems::class, 'package_id');
    }
}
