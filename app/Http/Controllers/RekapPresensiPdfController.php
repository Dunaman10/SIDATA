<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentAttendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RekapPresensiPdfController extends Controller
{
  public function export(Request $request, Student $student)
  {
    $academicYearId = $request->query('academic_year_id');
    $periode = 'Semua Periode';
    $academicYear = null;

    // Build query for attendance data
    $attendanceQuery = StudentAttendance::where('student_id', $student->id)
      ->with(['teachingJournal.lesson', 'teachingJournal.classes']);

    // Apply filter berdasarkan tahun akademik jika dipilih
    if ($academicYearId) {
      $academicYear = AcademicYear::find($academicYearId);

      if ($academicYear) {
        $periode = $academicYear->years . ' — ' . ucfirst($academicYear->semester);

        $attendanceQuery->whereHas('teachingJournal', function ($q) use ($academicYear) {
          $q->whereHas('schedule', fn($sq) => $sq->where('academic_year_id', $academicYear->id));
        });
      }
    }

    $attendances = $attendanceQuery->get();

    // Calculate summary
    $summary = [
      'hadir' => $attendances->where('status', 'Hadir')->count(),
      'izin'  => $attendances->where('status', 'Izin')->count(),
      'sakit' => $attendances->where('status', 'Sakit')->count(),
      'alfa'  => $attendances->where('status', 'Alfa')->count(),
      'total' => $attendances->count(),
    ];

    $persentaseHadir = $summary['total'] > 0
      ? round(($summary['hadir'] / $summary['total']) * 100, 1)
      : 0;

    // Detail attendance sorted by date
    $details = $attendances->sortBy(function ($att) {
      return $att->teachingJournal->date;
    })->map(function ($att) {
      return [
        'date'   => $att->teachingJournal->date->format('d M Y'),
        'lesson' => optional($att->teachingJournal->lesson)->name ?? '-',
        'class'  => optional($att->teachingJournal->classes)->class_name ?? '-',
        'status' => $att->status,
      ];
    });

    $data = [
      'student'          => $student,
      'summary'          => $summary,
      'details'          => $details,
      'periode'          => $periode,
      'persentaseHadir'  => $persentaseHadir,
    ];

    $pdf = Pdf::loadView('pdf.rekap-presensi', $data)
      ->setPaper('A4', 'portrait');

    return $pdf->stream("Rekap-Presensi-{$student->student_name}.pdf");
  }
}
