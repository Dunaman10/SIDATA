<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::find(179); // Alif Fauzan
auth()->login($user);

$controller = new \App\Http\Controllers\BulkRekapExcelController();
ob_start();
$response = $controller->export();
ob_end_clean();

echo "Response status: " . $response->getStatusCode() . "\n";
echo "Headers: \n";
print_r($response->headers->all());
echo "Content starts with: \n";
echo substr($response->getContent(), 0, 100);
