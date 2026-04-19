<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;

$teacherUserId = 179; // Alif Fauzan
$teacherId = 4;

$students = Student::with('memorizes')
    ->whereHas('pembimbing', function ($query) use ($teacherUserId) {
        $query->where('id_users', $teacherUserId);
    })->get();

echo "Students for mentor userId {$teacherUserId}:\n";
foreach ($students as $s) {
    echo "- ID: {$s->id}, Name: " . $s->student_name . "\n";
}
