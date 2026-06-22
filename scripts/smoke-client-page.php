<?php

/**
 * Integration smoke for client show page updates.
 * Run: php scripts/smoke-client-page.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Client;
use App\Models\User;
use App\Models\ClientStaffNote;
use Illuminate\Support\Facades\View;

$errors = [];

$user = User::query()->first();
if (! $user) {
    echo "SKIP: no users in database\n";
    exit(0);
}

$temp = false;
$client = Client::query()->first();

if (! $client) {
    $phone = '010' . random_int(10000000, 99999999);
    $client = Client::create([
        'name' => 'Smoke Test Client',
        'phone' => $phone,
        'phone_normalized' => $phone,
        'status' => 'prospect',
        'lead_stage' => 'lead',
        'created_by' => $user->id,
    ]);
    $temp = true;
    echo "Created temp client #{$client->id}\n";
} else {
    echo "Using client #{$client->id}\n";
}

try {
    $client->load(['sales.project', 'sales.salesRep', 'createdBy', 'staffNotes.user']);
    $themeColor = '#6366f1';
    $money = fn ($v) => number_format((float) $v, 0);
    $stageLabels = [
        'lead' => 'محتمل',
        'prospect' => 'مهتم',
        'proposal' => 'عرض',
        'negotiation' => 'تفاوض',
        'closed_won' => 'بيع',
        'closed_lost' => 'خسارة',
    ];

    foreach (['registration-meta', 'staff-notes', 'deals-list'] as $partial) {
        View::make("crm.clients.partials.{$partial}", compact('client', 'themeColor', 'stageLabels', 'money'))->render();
        echo "  OK  render partial [{$partial}]\n";
    }

    $note = ClientStaffNote::create([
        'client_id' => $client->id,
        'user_id' => $user->id,
        'type' => ClientStaffNote::TYPE_TIP,
        'body' => 'Smoke integration note',
    ]);
    echo "  OK  ClientStaffNote created #{$note->id}\n";
    $note->delete();
    echo "  OK  ClientStaffNote deleted\n";

    $url = route('crm.clients.staff-notes.store', $client);
    echo "  OK  route resolves: {$url}\n";
} catch (\Throwable $e) {
    $errors[] = $e->getMessage();
    echo " FAIL {$e->getMessage()}\n";
} finally {
    if ($temp) {
        $client->delete();
        echo "  OK  temp client cleaned up\n";
    }
}

if ($errors !== []) {
    exit(1);
}

echo "All client page integration checks passed.\n";
exit(0);
