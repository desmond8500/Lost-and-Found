<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Gestion d'une annonce par son auteur, sans compte :
 * l'accès se fait avec le lien secret reçu à la publication.
 */
class ManageItemController extends Controller
{
    public function show(Item $item, string $token): View
    {
        $this->ensureValidToken($item, $token);

        return view('items.manage', [
            'item' => $item,
            'token' => $token,
            'messages' => $item->messages()->latest()->get(),
        ]);
    }

    /**
     * Marque l'objet comme rendu : l'annonce disparaît des résultats.
     */
    public function resolve(Item $item, string $token): RedirectResponse
    {
        $this->ensureValidToken($item, $token);

        $item->forceFill(['resolved_at' => now()])->save();

        return redirect()
            ->to($item->manageUrl($token))
            ->with('status', 'Merci ! L\'annonce est marquée comme rendue et n\'apparaît plus dans les recherches.');
    }

    public function destroy(Item $item, string $token): RedirectResponse
    {
        $this->ensureValidToken($item, $token);

        $item->deleteWithPhoto();

        return redirect()
            ->route('items.index')
            ->with('status', 'Votre annonce a été supprimée.');
    }

    private function ensureValidToken(Item $item, string $token): void
    {
        abort_unless($item->hasManageToken($token), 404);
    }
}
