<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
  use HasFactory;

  public $timestamps = false;
  protected $fillable = [
    'student_name',
    'parent',
    'class_id',
    'profile',
    'tanggal_lahir',
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'parent');
  }

  public function class()
  {
    return $this->belongsTo(Classes::class, 'class_id');
  }

  public function attendances(): HasMany
  {
    return $this->hasMany(StudentAttendance::class);
  }

  public function memorizes()
  {
    return $this->hasMany(Memorize::class, 'id_student')
      ->latest('created_at');
  }

  public function latestMemorize()
  {
    return $this->hasOne(Memorize::class, 'id_student')->latestOfMany();
  }

  public function pembimbing()
  {
    return $this->belongsToMany(
      Teacher::class,
      'mentor_students',
      'id_student',
      'id_teacher'
    );
  }

  // Periode semester berdasarkan setoran terakhir santri
  public function getPeriodeAttribute()
  {
    // ambil tanggal setoran terakhir
    $last = $this->memorizes()
      ->latest('created_at')
      ->value('created_at');

    if (! $last) {
      return '-';
    }

    $date  = Carbon::parse($last);
    $year  = $date->year;
    $month = $date->month;

    // map semester ke teks Indonesia
    if ($month >= 1 && $month <= 6) {
      // Semester 1
      return "Januari - Juni {$year}";
    }

    // Semester 2
    return "Juli - Desember {$year}";
  }


  public function getRekap6BulanNilaiAttribute()
  {
    $values = $this->memorizes()
      ->where('created_at', '>=', now()->subMonths(6))
      ->pluck('nilai_avg')
      ->map(function ($val) {
        $val = trim($val);

        if (is_numeric($val)) {
          return floatval($val);
        }

        return match (strtoupper($val)) {
          'A' => 100,
          'B' => 75,
          'C' => 50,
          'D' => 25,
          default => 0,
        };
      });

    $avg = $values->avg();
  }

  public function getTotalSetoran6BulanAttribute()
  {
    return $this->memorizes()
      ->where('created_at', '>=', now()->subMonths(6))
      ->count();
  }

  public function getLastSetoranAttribute()
  {
    return $this->memorizes()
      ->latest()
      ->value('created_at') ?? null;
  }

  // Rata-rata nilai Makharijul Huruf
  public function getAvgMakharijulHurufAttribute()
  {
    // konversi huruf → angka
    $map = [
      'A' => 100,
      'B' => 75,
      'C' => 50,
      'D' => 25,
    ];

    $values = $this->memorizes()
      ->where('created_at', '>=', now()->subMonths(6))
      ->pluck('makharijul_huruf')
      ->map(fn($val) => $map[$val] ?? null)
      ->filter();

    if ($values->isEmpty()) {
      return '-';
    }

    $avg = $values->avg();

    // pakai range angka kamu
    if ($avg >= 75) {
      return 'A';
    } elseif ($avg >= 50) {
      return 'B';
    } elseif ($avg >= 25) {
      return 'C';
    } else {
      return 'D';
    }
  }

  // Rata-rata nilai Shifatul Huruf
  public function getAvgShifatulHurufAttribute()
  {
    $map = [
      'A' => 100,
      'B' => 75,
      'C' => 50,
      'D' => 25,
    ];

    $values = $this->memorizes()
      ->where('created_at', '>=', now()->subMonths(6))
      ->pluck('shifatul_huruf')
      ->map(fn($val) => $map[$val] ?? null)
      ->filter();

    if ($values->isEmpty()) return '-';

    $avg = $values->avg();

    return match (true) {
      $avg >= 75 => 'A',
      $avg >= 50 => 'B',
      $avg >= 25 => 'C',
      default => 'D',
    };
  }

  // Rata-rata nilai Akhkamul Qiroat
  public function getAvgAhkamulQiroatAttribute()
  {
    $map = [
      'A' => 100,
      'B' => 75,
      'C' => 50,
      'D' => 25,
    ];

    $values = $this->memorizes()
      ->where('created_at', '>=', now()->subMonths(6))
      ->pluck('ahkamul_qiroat')
      ->map(fn($val) => $map[$val] ?? null)
      ->filter();

    if ($values->isEmpty()) return '-';

    $avg = $values->avg();

    return match (true) {
      $avg >= 75 => 'A',
      $avg >= 50 => 'B',
      $avg >= 25 => 'C',
      default => 'D',
    };
  }

  // Rata-rata nilai Akhkamul Waqfi
  public function getAvgAhkamulWaqfiAttribute()
  {
    $map = [
      'A' => 100,
      'B' => 75,
      'C' => 50,
      'D' => 25,
    ];

    $values = $this->memorizes()
      ->where('created_at', '>=', now()->subMonths(6))
      ->pluck('ahkamul_waqfi')
      ->map(fn($val) => $map[$val] ?? null)
      ->filter();

    if ($values->isEmpty()) return '-';

    $avg = $values->avg();

    return match (true) {
      $avg >= 75 => 'A',
      $avg >= 50 => 'B',
      $avg >= 25 => 'C',
      default => 'D',
    };
  }

  // Rata-rata nilai Qowaid Tafsir
  public function getAvgQowaidTafsirAttribute()
  {
    $map = [
      'A' => 100,
      'B' => 75,
      'C' => 50,
      'D' => 25,
    ];

    $values = $this->memorizes()
      ->where('created_at', '>=', now()->subMonths(6))
      ->pluck('qowaid_tafsir')
      ->map(fn($val) => $map[$val] ?? null)
      ->filter();

    if ($values->isEmpty()) return '-';

    $avg = $values->avg();

    return match (true) {
      $avg >= 75 => 'A',
      $avg >= 50 => 'B',
      $avg >= 25 => 'C',
      default => 'D',
    };
  }

  // Rata-rata nilai Tarjamatul Ayat
  public function getAvgTarjamatulAyatAttribute()
  {
    $map = [
      'A' => 100,
      'B' => 75,
      'C' => 50,
      'D' => 25,
    ];

    $values = $this->memorizes()
      ->where('created_at', '>=', now()->subMonths(6))
      ->pluck('tarjamatul_ayat')
      ->map(fn($val) => $map[$val] ?? null)
      ->filter();

    if ($values->isEmpty()) return '-';

    $avg = $values->avg();

    return match (true) {
      $avg >= 75 => 'A',
      $avg >= 50 => 'B',
      $avg >= 25 => 'C',
      default => 'D',
    };
  }

  // Rata-rata Keseluruhan
  public function getAvgNilaiAttribute()
  {
    // mapping huruf → angka sesuai logika kamu
    $map = [
      'A' => 100,
      'B' => 75,
      'C' => 50,
      'D' => 25,
    ];

    // ambil rata-rata tiap komponen (huruf)
    $fields = [
      $this->avg_makharijul_huruf,
      $this->avg_shifatul_huruf,
      $this->avg_ahkamul_qiroat,
      $this->avg_ahkamul_waqfi,
      $this->avg_qowaid_tafsir,
      $this->avg_tarjamatul_ayat,
    ];

    // konversi huruf → angka
    $numeric = collect($fields)
      ->filter(fn($x) => $x !== '-' && isset($map[$x]))
      ->map(fn($x) => $map[$x]);

    if ($numeric->isEmpty()) {
      return '-';
    }

    // hitung rata-rata keseluruhan
    $avg = $numeric->avg();

    // kembalikan huruf sesuai range kamu
    return match (true) {
      $avg >= 75 => 'A',
      $avg >= 50 => 'B',
      $avg >= 25 => 'C',
      default => 'D',
    };
  }
}
