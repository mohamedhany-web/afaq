<?php

/**
 * End-to-end CRM client flows: create, filters, list columns.
 * Run: php scripts/smoke-crm-client-flows.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Client;
use App\Models\ClientStaffNote;
use App\Models\CrmFollowUp;
use App\Models\User;
use App\Services\ClientManagementService;
use App\Services\Crm\CrmFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

$errors = [];
$passed = 0;

function check(bool $ok, string $label, array &$errors, int &$passed): void
{
    if ($ok) {
        $passed++;
        echo "  OK  {$label}\n";
    } else {
        $errors[] = $label;
        echo " FAIL {$label}\n";
    }
}

echo "=== CRM client flow verification ===\n\n";

$user = User::query()->first();
if (! $user) {
    echo "SKIP: no users\n";
    exit(0);
}

$service = app(ClientManagementService::class);
$phone = '010' . random_int(20000000, 89999999);

$client = null;
try {
    check($service->canCreate($user), 'canCreate for user', $errors, $passed);

    $request = Request::create('/crm/clients', 'POST', [
        'name' => 'Smoke Flow Client',
        'phone' => $phone,
        'status' => 'prospect',
        'client_type' => 'individual',
    ]);

    $data = $service->prepareData($service->validate($request), $user, true);
    $client = Client::create($data);
    check($client->exists, 'Client::create succeeds', $errors, $passed);
    check($client->lead_stage === 'new', 'New client gets lead_stage=new', $errors, $passed);
    check(filled($client->phone_normalized), 'phone_normalized populated', $errors, $passed);

    ClientStaffNote::create([
        'client_id' => $client->id,
        'user_id' => $user->id,
        'type' => ClientStaffNote::TYPE_TIP,
        'body' => 'تعليق تجريبي للقائمة',
    ]);

    CrmFollowUp::create([
        'client_id' => $client->id,
        'user_id' => $user->id,
        'interaction_type' => 'call',
        'notes' => 'ملاحظة متابعة مكتملة',
        'status' => CrmFollowUp::STATUS_COMPLETED,
        'scheduled_at' => now()->subHour(),
        'completed_at' => now()->subMinutes(30),
    ]);

    CrmFollowUp::create([
        'client_id' => $client->id,
        'user_id' => $user->id,
        'interaction_type' => 'meeting',
        'notes' => 'موعد قادم',
        'status' => CrmFollowUp::STATUS_SCHEDULED,
        'scheduled_at' => now()->addDay(),
    ]);

    $loaded = Client::query()
        ->with(app(CrmFilterService::class)::for($user)->clientListRelations())
        ->find($client->id);

    $comment = $loaded->listLatestComment();
    check(is_string($comment) && filled($comment), 'Comment column shows latest note/comment', $errors, $passed);

    $next = $loaded->listNextAction();
    check(is_array($next) && ($next['label'] ?? '') !== '', 'Next Action column shows scheduled follow-up', $errors, $passed);

    View::make('crm.clients.partials.list-comment', ['client' => $loaded])->render();
    View::make('crm.clients.partials.list-next-action', ['client' => $loaded])->render();
    check(true, 'Comment/Next Action partials render with data', $errors, $passed);

    $filters = CrmFilterService::for($user);
    $mineCount = $filters->applyClientFilters(
        $filters->scope()->clientsQuery()->where('id', $client->id),
        Request::create('/', 'GET', ['mine' => '1']),
    )->count();
    check($mineCount >= 1, 'mine filter includes user-created client', $errors, $passed);

    $createdByCount = $filters->applyClientFilters(
        $filters->scope()->clientsQuery()->where('id', $client->id),
        Request::create('/', 'GET', ['created_by' => $user->id]),
    )->count();
    check($createdByCount === 1, 'created_by filter works', $errors, $passed);
} catch (\Throwable $e) {
    check(false, 'Client flow — ' . $e->getMessage(), $errors, $passed);
} finally {
    if ($client) {
        CrmFollowUp::query()->where('client_id', $client->id)->delete();
        ClientStaffNote::query()->where('client_id', $client->id)->delete();
        $client->delete();
        check(true, 'Test client cleaned up', $errors, $passed);
    }
}

echo "\n=== Results: {$passed} passed, " . count($errors) . " failed ===\n";
exit($errors === [] ? 0 : 1);
