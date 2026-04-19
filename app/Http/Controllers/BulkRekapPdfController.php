<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;

class BulkRekapPdfController extends Controller
{
  public function export()
  {
    $teacherUserId = auth()->id();

    // Pastikan hanya santri yang diampu oleh teacher yang sedang login
    $students = Student::with(['memorizes', 'pembimbing', 'class'])
      ->whereHas('pembimbing', function (Builder $query) use ($teacherUserId) {
        $query->where('id_users', $teacherUserId);
      })
      ->get();

    // Siapkan data untuk view
    $studentData = $students->map(function ($student) {
      return [
        'student' => $student,
        'periode' => $student->periode,
        'memorizes' => $student->memorizes()->get(),
      ];
    });

    $data = [
      'studentData' => $studentData,
    ];

    $pdf = Pdf::loadView('pdf.bulk-rekap', $data)
      ->setPaper('A4', 'portrait');

    return $pdf->stream("Rekap-Semua-Santri.pdf");
  }
}
