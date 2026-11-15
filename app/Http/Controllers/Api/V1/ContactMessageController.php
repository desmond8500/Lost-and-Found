<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageReceived;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    /**
     * Transmet un message à l'auteur de l'annonce, par e-mail.
     */
    public function store(StoreContactMessageRequest $request, Item $item): JsonResponse
    {
        abort_if($item->isResolved(), 410, 'Cet objet a déjà été rendu.');

        $message = $item->messages()->create($request->safe()->except('website'));

        Mail::to($item->contact_email)->send(new ContactMessageReceived($message));

        return response()->json([
            'message' => 'Votre message a été transmis. La personne vous répondra directement par e-mail.',
        ], 201);
    }
}
