<?php

namespace App\Http\Controllers;

use App\Models\RealEstateDeveloper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeveloperSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['developers' => []]);
        }

        $query = RealEstateDeveloper::query()
            ->where('name', 'like', '%' . $term . '%');

        if ($request->boolean('contracted')) {
            $query->contracted();
        }

        $developers = $query->orderBy('name')->limit(25)->get(['id', 'name', 'phone', 'email']);

        return response()->json([
            'developers' => $developers->map(fn (RealEstateDeveloper $d) => [
                'id' => $d->id,
                'name' => $d->name,
                'label' => self::formatLabel($d),
            ])->values(),
        ]);
    }

    public static function formatLabel(RealEstateDeveloper $developer): string
    {
        $parts = [$developer->name];

        if ($developer->phone) {
            $parts[] = $developer->phone;
        }

        return implode(' — ', $parts);
    }
}
