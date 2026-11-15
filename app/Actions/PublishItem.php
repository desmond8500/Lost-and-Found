<?php

namespace App\Actions;

use App\Mail\ItemPublished;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;

/**
 * Publie une annonce (site web et API mobile) et envoie
 * le lien de gestion à son auteur.
 */
class PublishItem
{
    /**
     * @param  array<string, mixed>  $attributes  Données validées de l'annonce
     * @return array{0: Item, 1: string} L'annonce et son jeton de gestion en clair
     */
    public function handle(array $attributes, ?UploadedFile $photo = null): array
    {
        $item = new Item($attributes);

        if ($photo) {
            $item->photo_path = $photo->store('items', 'public');
        }

        $token = $item->issueManageToken();
        $item->save();

        Mail::to($item->contact_email)->send(new ItemPublished($item, $token));

        return [$item, $token];
    }
}
