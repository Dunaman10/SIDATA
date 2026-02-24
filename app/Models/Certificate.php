<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
  protected $fillable = [
    'student_id',
    'title',
    'description',
    'file_path',
    'issued_date',
  ];

  public function student()
  {
    return $this->belongsTo(Student::class);
  }
}
