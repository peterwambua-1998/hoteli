<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    public function departmentItems()
    {
        return $this->hasMany(DepartmentItem::class, 'department_id');
    }
}
