<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapPdfController extends Controller
{
  public function export(Student $student)
  {
    $data = [
      'student' => $student,
      'periode' => $student->periode, // accessor yg udah kita bikin
      'memorizes' => $student->memorizes()
        ->with('surah')
        ->where('created_at', '>=', now()->subMonths(6))
        ->get(),
    ];

    $pdf = Pdf::loadView('pdf.rekap', $data)
      ->setPaper('A4', 'portrait');

    return $pdf->stream("Rekap-{$student->student_name}.pdf");
  }
}
