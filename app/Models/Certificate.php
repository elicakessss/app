<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'issued_at',
        'file_path',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
