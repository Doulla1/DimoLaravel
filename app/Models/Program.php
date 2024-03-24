<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'illustration',
        'start_date',
        'end_date',
    ];

    // Get the users with role "student" in this classroom
    public function students()
    {
        return $this->belongsToMany(User::class, "students");
    }

    // Get the users with role "teacher" in this classroom*
    public function teachers()
    {
        return $this->belongsToMany(User::class,"teachers");
    }
}
