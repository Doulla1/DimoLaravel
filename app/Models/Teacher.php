<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends User
{
    use HasFactory;

    // Get the classrooms teached by this user (teacher
    public function teachedClassrooms()
    {
        return $this->belongsToMany(Program::class, "teachers");
    }
}
