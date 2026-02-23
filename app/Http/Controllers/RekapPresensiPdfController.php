<?php

namespace App\Http\Controllers;

use App\Filament\Teacher\Resources\RekapPresensiResource;
use App\Models\Student;
use App\Models\StudentAttendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RekapPresensiPdfController extends Controller
{
  public function export(Request $request, Student $student)
  {
    $semesterParam = $request->query('semester');

    $from = null;
    $until = null;
    $periode = 'Semua Periode';

    if ($semesterParam) {
      $range = RekapPresensiResource::getDateRangeFromSemester($semesterParam);
      $from = $range['from'];
      $until = $range['until'];

      [$semester, $year] = explode('|', $semesterParam);
      if ($semester == '1') {
        $periode = "Semester 1 — Juli - Desember {$year}";
      } else {
        $periode = "Semester 2 — Januari - Juni {$year}";
      }
    }

    // Build query for attendance data
    $attendanceQuery = StudentAttendance::where('student_id', $student->id)
      ->with(['teachingJournal.lesson', 'teachingJournal.classes']);

    // Apply date filters
    if ($from && $until) {
      $attendanceQuery->whereHas('teachingJournal', function ($q) use ($from, $until) {
        $q->whereDate('date', '>=', $from)
          ->whereDate('date', '<=', $until);
      });
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

    // Detail attendance sorted by date
    $details = $attendances->sortBy(function ($att) {
      return $att->teachingJournal->date;
    })->map(function ($att) {
      return [
        'date'    => $att->teachingJournal->date->format('d M Y'),
        'lesson'  => optional($att->teachingJournal->lesson)->name ?? '-',
        'class'   => optional($att->teachingJournal->classes)->class_name ?? '-',
        'status'  => $att->status,
      ];
    });

    $data = [
      'student'  => $student,
      'summary'  => $summary,
      'details'  => $details,
      'periode'  => $periode,
    ];

    $pdf = Pdf::loadView('pdf.rekap-presensi', $data)
      ->setPaper('A4', 'portrait');

    return $pdf->stream("Rekap-Presensi-{$student->student_name}.pdf");
  }
}
