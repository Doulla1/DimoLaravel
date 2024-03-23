<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    // Get the users with role "student" in this classroom
    public function students()
    {
        return $this->belongsToMany(User::class)->wherePivot('role', 'student');
    }

    // Get the users with role "teacher" in this classroom*
    public function teachers()
    {
        return $this->belongsToMany(User::class)->wherePivot('role', 'teacher');
    }
}
