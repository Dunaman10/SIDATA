<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
  use HasFactory;

  public $timestamps = false;
  protected $fillable = ['id_users'];

  public function user()
  {
    return $this->belongsTo(User::class, 'id_users');
  }

  public function classes()
  {
    return $this->belongsToMany(Classes::class, 'class_teacher', 'id_teacher', 'id_class');
  }

  public function memorizes()
  {
    return $this->hasMany(Memorize::class, 'id_teacher');
  }

  public function students()
  {
    return Student::whereIn('class_id', $this->classes()->select('classes.id'));
  }

  public function binaan()
  {
    return $this->hasMany(MentorStudent::class, 'id_teacher');
  }

  public function schedules(): HasMany
  {
    return $this->hasMany(Schedule::class);
  }

  public function teachingJournals(): HasMany
  {
    return $this->hasMany(TeachingJournal::class);
  }

  protected static function booted()
  {
    static::deleted(function ($teacher) {
      $teacher->user()->delete();
    });
  }
}
