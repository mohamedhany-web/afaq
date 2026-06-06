<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\CrmScopeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['clients' => []]);
        }

        $user = $request->user();
        $useCrmScope = $request->boolean('crm_scope')
            || ($user && $user->canAccessCrm() && $user->usesCrmWorkspace());

        $query = $useCrmScope && $user
            ? CrmScopeService::for($user)->clientsQuery()
            : Client::query();

        $like = '%' . $term . '%';

        $clients = $query
            ->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('company_name', 'like', $like);
            })
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'name', 'phone', 'company_name', 'email']);

        return response()->json([
            'clients' => $clients->map(fn (Client $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone,
                'label' => self::formatLabel($c),
            ])->values(),
        ]);
    }

    public static function formatLabel(Client $client): string
    {
        $parts = [$client->name];

        if ($client->phone) {
            $parts[] = $client->phone;
        }

        if ($client->company_name) {
            $parts[] = $client->company_name;
        }

        return implode(' — ', $parts);
    }
}
