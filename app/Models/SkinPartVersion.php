<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkinPartVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'skin_part_id',
        'name',
        'image',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'skin_part_id' => $this->skin_part_id,
            'name' => $this->name,
            'image' => $this->image,
        ];
    }

}
