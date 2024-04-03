<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'question_id',
        'is_correct',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'question_id' => $this->question_id,
        ];
    }
}
