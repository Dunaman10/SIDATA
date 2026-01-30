<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  protected $fillable = [
    'title',
    'subtitle',
    'banner_image',
    'about',
    'vision',
    'mission',
    'phone',
    'email',
    'address',
  ];
}
