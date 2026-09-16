<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Support\OwnerDiscovery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function store(Request $request, string $kind, int $target)
    {
        $v = $request->validate(['saved' => ['required', 'boolean']]);
        ($kind === 'vet' ? OwnerDiscovery::vets() : OwnerDiscovery::listings())->findOrFail($target);
        $key = ['user_id' => $request->user()->id, 'kind' => $kind, 'target_id' => $target];
        if ($v['saved']) {
            DB::table('owner_favorites')->upsert([array_merge($key, ['created_at' => now(), 'updated_at' => now()])], ['user_id', 'kind', 'target_id'], ['updated_at']);
        } else {
            DB::table('owner_favorites')->where($key)->delete();
        }

        return $request->expectsJson() ? response()->json(['saved' => (bool) $v['saved']]) : back()->with('success', $v['saved'] ? 'Saved to your favorites.' : 'Removed from favorites.');
    }
}
