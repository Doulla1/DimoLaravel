<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'illustration', 'program_id'];

    // Got many documents
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Got many courses
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Got many programs
    public function programs()
    {
        return $this->belongsToMany(Program::class, "program_subject");
    }

    // Got many questionnaires
    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class);
    }

    // Got many teachers
    public function teachers()
    {
        return $this->belongsToMany(User::class, "teachers");
    }
}
