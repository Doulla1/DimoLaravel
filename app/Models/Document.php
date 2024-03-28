<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'file_path', 'subject_id'];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'file_path' => $this->file_path,
            'subject_id' => $this->subject_id
        ];
    }
}
