<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skin2 extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hair_version',
        'hair_color',
        'upper_body_color',
        'lower_body_color',
        'skin_color'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toArray(): array
    {
        return [
            'hair_version' => $this->hair_version,
            'hair_color' => $this->hair_color,
            'upper_body_color' => $this->upper_body_color,
            'lower_body_color' => $this->lower_body_color,
            'skin_color' => $this->skin_color
        ];
    }

}
