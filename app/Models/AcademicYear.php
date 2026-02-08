<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
  protected $fillable = [
    'years',
    'semester',
    'is_active'
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function isActive($query)
  {
    return $query->where('is_active', true);
  }
}
