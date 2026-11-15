<x-mail::message>
# Nouveau message

**{{ $contactMessage->sender_name }}** vous a écrit au sujet de votre annonce **{{ $item->title }}** :

<x-mail::panel>
{{ $contactMessage->body }}
</x-mail::panel>

Répondez simplement à cet e-mail pour lui écrire ({{ $contactMessage->sender_email }}).

Pour votre sécurité : ne payez jamais pour récupérer un objet et donnez rendez-vous dans un lieu public (commissariat, mairie, gare…).

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
