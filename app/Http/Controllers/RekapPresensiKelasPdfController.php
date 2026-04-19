<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RekapPresensiKelasPdfController extends Controller
{
  public function export(Request $request, Classes $class)
  {
    $academicYearId = $request->query('academic_year_id');
    $teacherId      = $request->query('teacher_id');

    $academicYear = $academicYearId ? AcademicYear::find($academicYearId) : null;
    $teacher      = $teacherId ? Teacher::find($teacherId) : null;

    $periode = $academicYear
      ? $academicYear->years . ' — ' . ucfirst($academicYear->semester)
      : 'Semua Periode';

    // Ambil schedule IDs milik teacher di kelas ini pada tahun akademik yang dipilih
    $scheduleIds = $academicYear && $teacher
      ? Schedule::where('teacher_id', $teacher->id)
          ->where('classes_id', $class->id)
          ->where('academic_year_id', $academicYear->id)
          ->pluck('id')
      : collect();

    // Ambil semua santri di kelas ini, urut nama
    $students = Student::where('class_id', $class->id)
      ->orderBy('student_name')
      ->get();

    // Hitung rekap kehadiran per santri
    $rekapData = $students->map(function (Student $student) use ($teacher, $academicYear, $scheduleIds) {
      $query = StudentAttendance::where('student_id', $student->id);

      // Filter by guru
      if ($teacher) {
        $query->whereHas('teachingJournal', fn($q) => $q->where('teacher_id', $teacher->id));
      }

      // Filter by tahun akademik:
      // Prioritas 1: via schedule_id (data baru yang sudah punya schedule_id)
      // Prioritas 2: tidak filter schedule (tampilkan semua dari guru ini di kelas ini)
      // Jika ada scheduleIds, filter via schedule_id ATAU journal tanpa schedule (nullable)
      if ($academicYear) {
        if ($scheduleIds->isNotEmpty()) {
          $query->whereHas('teachingJournal', function ($q) use ($scheduleIds) {
            $q->where(function ($inner) use ($scheduleIds) {
              // Jurnal yang punya schedule_id dan sesuai academic year
              $inner->whereIn('schedule_id', $scheduleIds)
                // ATAU jurnal yang schedule_id-nya null (data lama) — tetap disertakan
                ->orWhereNull('schedule_id');
            });
          });
        }
      }

      $attendances = $query->get();
      $total  = $attendances->count();
      $hadir  = $attendances->where('status', 'Hadir')->count();
      $izin   = $attendances->where('status', 'Izin')->count();
      $sakit  = $attendances->where('status', 'Sakit')->count();
      $alfa   = $attendances->where('status', 'Alfa')->count();
      $persen = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;

      return [
        'nama'   => $student->student_name,
        'total'  => $total,
        'hadir'  => $hadir,
        'izin'   => $izin,
        'sakit'  => $sakit,
        'alfa'   => $alfa,
        'persen' => $persen,
      ];
    });

    $data = [
      'class'       => $class,
      'teacher'     => $teacher,
      'periode'     => $periode,
      'rekapData'   => $rekapData,
      'totalSantri' => $students->count(),
    ];

    $pdf = Pdf::loadView('pdf.rekap-presensi-kelas', $data)
      ->setPaper('A4', 'landscape');

    return $pdf->stream("Rekap-Presensi-{$class->class_name}-{$periode}.pdf");
  }
}
