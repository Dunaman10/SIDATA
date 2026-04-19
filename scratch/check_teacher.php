<?php

use App\Models\TeachingJournal;
use App\Models\Teacher;
use App\Models\User;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Teachers in Journals ---\n";
$teacherIds = TeachingJournal::select('teacher_id')->distinct()->pluck('teacher_id');
foreach ($teacherIds as $tid) {
    $teacher = Teacher::with('user')->find($tid);
    $count = TeachingJournal::where('teacher_id', $tid)->count();
    echo "Teacher ID: {$tid}, Name: " . ($teacher->user->name ?? 'N/A') . ", Count: {$count}\n";
}

echo "\n--- All Teachers ---\n";
foreach (Teacher::with('user')->get() as $t) {
    echo "Teacher ID: {$t->id}, User ID: {$t->id_users}, User Name: " . ($t->user->name ?? 'N/A') . "\n";
}
