<x-mail::message>
# Votre annonce est en ligne

Bonjour {{ $item->contact_name }},

Votre annonce **{{ $item->title }}** ({{ $item->type->label() }}, {{ $item->city }}) est publiée.

Ce lien secret vous permet de lire les messages reçus, de marquer l'objet comme rendu ou de supprimer l'annonce. **Ne le partagez pas.**

<x-mail::button :url="$manageUrl">
Gérer mon annonce
</x-mail::button>

Lien public de l'annonce : {{ $showUrl }}

Merci,<br>
{{ config('app.name') }}
</x-mail::message>
