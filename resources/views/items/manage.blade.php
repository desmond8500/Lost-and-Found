<x-layout title="Gérer mon annonce">
    <h1 class="mb-2 text-2xl font-semibold tracking-tight">Gérer mon annonce</h1>

    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
        <p class="font-semibold">Votre lien secret</p>
        <p class="mt-1">Toute personne qui possède ce lien peut gérer l'annonce. Ne le partagez pas.</p>
        <input type="text" readonly value="{{ $item->manageUrl($token) }}" class="input mt-2 font-mono text-xs" onclick="this.select()">
    </div>

    <div class="grid gap-6 lg:grid-cols-5">
        <section class="card lg:col-span-2">
            <div class="mb-2 flex items-center gap-2 text-sm">
                <span class="rounded-full px-2.5 py-0.5 font-semibold {{ $item->type->badgeClasses() }}">{{ $item->type->label() }}</span>
                @if ($item->isResolved())
                    <span class="rounded-full bg-stone-200 px-2.5 py-0.5 font-semibold text-stone-700">Rendu le {{ $item->resolved_at->format('d/m/Y') }}</span>
                @endif
            </div>
            <h2 class="text-lg font-semibold">{{ $item->title }}</h2>
            <p class="mt-1 text-sm text-stone-600">{{ $item->city }} · le {{ $item->occurred_on->format('d/m/Y') }}</p>
            <a href="{{ route('items.show', $item) }}" class="mt-3 inline-block text-sm text-amber-700 underline">Voir l'annonce publique</a>

            <div class="mt-6 flex flex-col gap-3 border-t border-stone-200 pt-6">
                @unless ($item->isResolved())
                    <form method="POST" action="{{ route('items.resolve', ['item' => $item, 'token' => $token]) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-primary w-full">L'objet a été rendu</button>
                    </form>
                @endunless

                <form method="POST" action="{{ route('items.destroy', ['item' => $item, 'token' => $token]) }}"
                    onsubmit="return confirm('Supprimer définitivement cette annonce et ses messages ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger w-full">Supprimer l'annonce</button>
                </form>
            </div>
        </section>

        <section class="lg:col-span-3">
            <h2 class="mb-3 text-lg font-semibold">Messages reçus ({{ $messages->count() }})</h2>

            @forelse ($messages as $message)
                <article class="card mb-3">
                    <header class="mb-2 flex flex-wrap items-baseline justify-between gap-2">
                        <p class="font-medium">{{ $message->sender_name }}</p>
                        <time class="text-xs text-stone-500" datetime="{{ $message->created_at->toIso8601String() }}">
                            {{ $message->created_at->format('d/m/Y à H:i') }}
                        </time>
                    </header>
                    <p class="whitespace-pre-line text-sm text-stone-700">{{ $message->body }}</p>
                    <a href="mailto:{{ $message->sender_email }}?subject={{ rawurlencode('Re: '.$item->title) }}" class="btn-secondary mt-3">Répondre par e-mail</a>
                </article>
            @empty
                <div class="card text-sm text-stone-600">
                    Aucun message pour l'instant. Vous serez prévenu par e-mail dès que quelqu'un vous écrit.
                </div>
            @endforelse
        </section>
    </div>
</x-layout>
