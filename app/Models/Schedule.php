<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
  protected $fillable = [
    'academic_year_id',
    'classes_id',
    'lesson_id',
    'teacher_id',
    'day_of_week',
    'start_time',
    'end_time',
  ];

  public function academicYear(): BelongsTo
  {
    return $this->belongsTo(AcademicYear::class);
  }

  public function classes(): BelongsTo
  {
    return $this->belongsTo(Classes::class);
  }

  public function lesson(): BelongsTo
  {
    return $this->belongsTo(Lesson::class);
  }

  public function teacher(): BelongsTo
  {
    return $this->belongsTo(Teacher::class);
  }
}
