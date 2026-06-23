<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['clients', 'crm_follow_ups', 'crm_tasks', 'client_staff_notes'];
foreach ($tables as $t) {
    echo $t . ': ' . (\Illuminate\Support\Facades\Schema::hasTable($t) ? 'YES' : 'MISSING') . "\n";
}
