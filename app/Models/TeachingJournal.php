<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingJournal extends Model
{
  protected $fillable = [
    'schedule_id',
    'teacher_id',
    'classes_id',
    'lesson_id',
    'date',
    'start_time',
    'end_time',
    'topic',
    'notes',
    'photo',
  ];

  protected $casts = [
    'date' => 'date',
    'started_at' => 'datetime',
    'ended_at' => 'datetime',
  ];

  public function classes(): BelongsTo
  {
    return $this->belongsTo(Classes::class);
  }

  public function teacher(): BelongsTo
  {
    return $this->belongsTo(Teacher::class);
  }

  public function lesson(): BelongsTo
  {
    return $this->belongsTo(Lesson::class);
  }

  // Relasi ke detail kehadiran siswa
  public function studentAttendances(): HasMany
  {
    return $this->hasMany(StudentAttendance::class);
  }
}
