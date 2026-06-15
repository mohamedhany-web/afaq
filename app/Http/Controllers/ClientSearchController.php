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

        if (! $user || (! $user->can('view-clients') && ! $user->can('viewAny', Client::class))) {
            return response()->json(['clients' => []]);
        }

        $query = $user->can('viewAny', Client::class)
            ? Client::query()
            : CrmScopeService::for($user)->clientsQuery();

        $like = '%' . $term . '%';

        $clients = $query
            ->where(function ($q) use ($like, $user) {
                $q->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('company_name', 'like', $like);

                if ($user->can('viewAny', Client::class)) {
                    $q->orWhere('email', 'like', $like);
                }
            })
            ->orderBy('name')
            ->limit(30)
            ->get($user->can('viewAny', Client::class)
                ? ['id', 'name', 'phone', 'company_name', 'email']
                : ['id', 'name', 'phone', 'company_name']);

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
