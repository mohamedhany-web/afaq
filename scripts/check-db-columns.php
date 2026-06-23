<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$cols = \Illuminate\Support\Facades\Schema::getColumnListing('clients');
echo "clients columns:\n" . implode(', ', $cols) . "\n\n";

$needed = ['phone_normalized', 'lost_reason', 'lost_reason_notes', 'lost_at', 'description', 'created_by', 'lead_stage'];
foreach ($needed as $col) {
    echo ($col . ': ' . (in_array($col, $cols, true) ? 'YES' : 'MISSING') . "\n");
}
