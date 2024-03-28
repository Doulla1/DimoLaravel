<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'questionnaire_id',
        'result',
        'total_correct_answers',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'questionnaire_id' => $this->questionnaire_id,
            'result' => $this->result,
            'total_correct_answers' => $this->total_correct_answers,
        ];
    }

}
