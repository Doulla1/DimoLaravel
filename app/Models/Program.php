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

    // Get subjects of this program
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    // Get the users with role "student" in this classroom
    public function students()
    {
        return $this->belongsToMany(User::class, "students");
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'illustration' => $this->illustration,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }

}
