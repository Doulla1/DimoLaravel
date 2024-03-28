<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'skin_part_version_id',
        'color'
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'skin_part_version_id' => $this->skin_part_version_id,
            'color' => $this->color
        ];
    }

}
