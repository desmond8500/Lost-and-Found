<?php

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Représentation publique d'une annonce.
 * L'e-mail de l'auteur et l'empreinte du jeton ne sont jamais exposés.
 *
 * @mixin Item
 */
class ItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => [
                'value' => $this->type->value,
                'label' => $this->type->label(),
            ],
            'category' => [
                'value' => $this->category->value,
                'label' => $this->category->label(),
                'is_official_document' => $this->category->isOfficialDocument(),
            ],
            'title' => $this->title,
            'description' => $this->description,
            'name_on_item' => $this->name_on_item,
            'city' => $this->city,
            'location' => $this->location,
            'occurred_on' => $this->occurred_on->toDateString(),
            'photo_url' => $this->photoUrl(),
            'contact_name' => $this->contact_name,
            'is_resolved' => $this->isResolved(),
            'resolved_at' => $this->resolved_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'web_url' => route('items.show', $this->resource),
            'messages' => ContactMessageResource::collection($this->whenLoaded('messages')),
        ];
    }
}
