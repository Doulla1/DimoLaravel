<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    // Got many documents
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // Got many questionnaires
    public function questionnaires()
    {
        return $this->hasMany(Questionnaire::class);
    }
}
