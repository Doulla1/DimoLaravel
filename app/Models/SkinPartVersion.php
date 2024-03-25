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

}
