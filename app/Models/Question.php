<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'questionnaire_id',
        'order'
    ];

    // Get the options for the question.
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'questionnaire_id' => $this->questionnaire_id,
            'order' => $this->order
        ];
    }
}
