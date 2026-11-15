<?php

namespace App\Http\Controllers;

use App\Actions\PublishItem;
use App\Enums\ItemType;
use App\Http\Requests\SearchItemsRequest;
use App\Http\Requests\StoreItemRequest;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Liste des annonces ouvertes, avec recherche et filtres.
     */
    public function index(SearchItemsRequest $request): View
    {
        $filters = $request->validated();

        $items = Item::query()
            ->open()
            ->filter($filters)
            ->latest('occurred_on')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('items.index', [
            'items' => $items,
            'filters' => $filters,
        ]);
    }

    /**
     * Formulaire de publication.
     */
    public function create(Request $request): View
    {
        return view('items.create', [
            'type' => ItemType::tryFrom((string) $request->query('type')) ?? ItemType::Found,
        ]);
    }

    /**
     * Enregistre l'annonce et envoie le lien de gestion à son auteur.
     */
    public function store(StoreItemRequest $request, PublishItem $publishItem): RedirectResponse
    {
        [$item, $token] = $publishItem->handle(
            $request->safe()->except(['photo', 'website']),
            $request->file('photo'),
        );

        return redirect()
            ->to($item->manageUrl($token))
            ->with('status', 'Votre annonce est publiée. Gardez précieusement le lien de cette page : il vous a aussi été envoyé par e-mail.');
    }

    /**
     * Fiche d'une annonce, avec le formulaire de contact.
     */
    public function show(Item $item): View
    {
        return view('items.show', ['item' => $item]);
    }
}
