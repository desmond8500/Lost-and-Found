<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\PublishItem;
use App\Enums\ItemCategory;
use App\Enums\ItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchItemsRequest;
use App\Http\Requests\StoreItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ItemController extends Controller
{
    /**
     * Annonces ouvertes, paginées, avec les mêmes filtres que le site.
     */
    public function index(SearchItemsRequest $request): AnonymousResourceCollection
    {
        $items = Item::query()
            ->open()
            ->filter($request->validated())
            ->latest('occurred_on')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return ItemResource::collection($items);
    }

    public function show(Item $item): ItemResource
    {
        return new ItemResource($item);
    }

    /**
     * Publie une annonce. Le jeton de gestion n'est renvoyé qu'ici :
     * l'application doit le conserver sur l'appareil.
     */
    public function store(StoreItemRequest $request, PublishItem $publishItem): JsonResponse
    {
        [$item, $token] = $publishItem->handle(
            $request->safe()->except(['photo', 'website']),
            $request->file('photo'),
        );

        return (new ItemResource($item))
            ->additional([
                'manage_token' => $token,
                'manage_url' => $item->manageUrl($token),
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Types et catégories avec leurs libellés, pour les formulaires de l'application.
     */
    public function meta(): JsonResponse
    {
        $map = fn (ItemType|ItemCategory $case) => ['value' => $case->value, 'label' => $case->label()];

        return response()->json([
            'types' => array_map($map, ItemType::cases()),
            'categories' => array_map($map, ItemCategory::cases()),
        ]);
    }
}
