<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageReceived;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    /**
     * Transmet un message à l'auteur de l'annonce sans jamais
     * révéler son adresse e-mail : il répond directement par e-mail.
     */
    public function store(StoreContactMessageRequest $request, Item $item): RedirectResponse
    {
        abort_if($item->isResolved(), 410, 'Cet objet a déjà été rendu.');

        $message = $item->messages()->create($request->safe()->except('website'));

        Mail::to($item->contact_email)->send(new ContactMessageReceived($message));

        return redirect()
            ->route('items.show', $item)
            ->with('status', 'Votre message a été transmis. La personne vous répondra directement par e-mail.');
    }
}
