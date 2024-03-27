<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'lesson_id',
        'is_visible'
    ];

    // Get the questions for the questionnaire.
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

}
