<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends User
{
    use HasFactory;

    // Get the classrooms attended by this user (student)
    public function attendedClassrooms()
    {
        return $this->belongsToMany(Program::class,"students");
    }
}
