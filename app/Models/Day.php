<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function endBy()
    {
        return $this->belongsTo(User::class, 'ended_by');
    }
}
