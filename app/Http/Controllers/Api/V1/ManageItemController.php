<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Gestion d'une annonce par son auteur depuis l'application.
 * Le jeton de gestion est transmis dans l'en-tête X-Manage-Token
 * (jamais dans l'URL, pour ne pas apparaître dans les journaux).
 */
class ManageItemController extends Controller
{
    public function show(Request $request, Item $item): ItemResource
    {
        $this->ensureValidToken($request, $item);

        return new ItemResource($item->load(['messages' => fn ($query) => $query->latest()]));
    }

    public function resolve(Request $request, Item $item): ItemResource
    {
        $this->ensureValidToken($request, $item);

        if (! $item->isResolved()) {
            $item->forceFill(['resolved_at' => now()])->save();
        }

        return new ItemResource($item);
    }

    public function destroy(Request $request, Item $item): Response
    {
        $this->ensureValidToken($request, $item);

        $item->deleteWithPhoto();

        return response()->noContent();
    }

    private function ensureValidToken(Request $request, Item $item): void
    {
        abort_unless($item->hasManageToken((string) $request->header('X-Manage-Token')), 404);
    }
}
