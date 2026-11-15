<?php

namespace App\Http\Controllers;

use App\Enums\ItemCategory;
use App\Enums\ItemType;
use App\Http\Requests\StoreItemRequest;
use App\Mail\ItemPublished;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller
{
    /**
     * Liste des annonces ouvertes, avec recherche et filtres.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::enum(ItemType::class)],
            'category' => ['nullable', Rule::enum(ItemCategory::class)],
            'city' => ['nullable', 'string', 'max:80'],
        ]);

        $items = Item::query()
            ->open()
            ->search($filters['q'] ?? null)
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->when($filters['city'] ?? null, fn ($query, $city) => $query->whereLike('city', "%{$city}%"))
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
    public function store(StoreItemRequest $request): RedirectResponse
    {
        $item = new Item($request->safe()->except(['photo', 'website']));

        if ($request->hasFile('photo')) {
            $item->photo_path = $request->file('photo')->store('items', 'public');
        }

        $token = $item->issueManageToken();
        $item->save();

        Mail::to($item->contact_email)->send(new ItemPublished($item, $token));

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
