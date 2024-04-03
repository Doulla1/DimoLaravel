<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkinPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(SkinPartVersion::class);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'versions' => $this->versions->toArray()
        ];
    }
}
