<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
  use HasFactory;

  protected $table = 'classes';
  protected $fillable = ['class_name'];
  public $timestamps = false;

  public function students()
  {
    return $this->hasMany(Student::class, 'class_id');
  }

  public function teachers()
  {
    return $this->belongsToMany(Teacher::class, 'class_teacher', 'id_class', 'id_teacher');
  }

  public function schedules(): HasMany
  {
    return $this->hasMany(Schedule::class);
  }

  public function teachingJournals(): HasMany
  {
    return $this->hasMany(TeachingJournal::class);
  }
}
