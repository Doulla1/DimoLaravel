<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'question_id',
        'option_id',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'question_id' => $this->question_id,
            'option_id' => $this->option_id,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
