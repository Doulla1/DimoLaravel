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

    // Got many questionnaires
    public function questionnaires(): HasMany
    {
        return $this->hasMany(Questionnaire::class);
    }

    // Got many teachers
    public function teachers()
    {
        return $this->belongsToMany(User::class, "teachers");
    }

    public function program(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'illustration' => $this->illustration,
            'program_id' => $this->program_id
        ];
    }
}
