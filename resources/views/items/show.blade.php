<x-layout :title="$item->title">
    <a href="{{ route('items.index') }}" class="mb-6 inline-block text-sm text-stone-600 hover:text-stone-900">← Toutes les annonces</a>

    <div class="grid gap-6 lg:grid-cols-5">
        <article class="card lg:col-span-3">
            <div class="mb-3 flex flex-wrap items-center gap-2 text-sm">
                <span class="rounded-full px-2.5 py-0.5 font-semibold {{ $item->type->badgeClasses() }}">{{ $item->type->label() }}</span>
                <span class="text-stone-500">{{ $item->category->label() }}</span>
                @if ($item->isResolved())
                    <span class="rounded-full bg-stone-200 px-2.5 py-0.5 font-semibold text-stone-700">Rendu</span>
                @endif
            </div>

            <h1 class="text-2xl font-semibold tracking-tight">{{ $item->title }}</h1>

            @if ($item->photoUrl())
                <img src="{{ $item->photoUrl() }}" alt="Photo de l'objet" class="mt-4 max-h-96 w-full rounded-xl object-contain bg-stone-100">
            @endif

            <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                @if ($item->name_on_item)
                    <div>
                        <dt class="text-stone-500">Nom inscrit</dt>
                        <dd class="font-medium">{{ $item->name_on_item }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-stone-500">{{ $item->type === \App\Enums\ItemType::Lost ? 'Perdu le' : 'Trouvé le' }}</dt>
                    <dd class="font-medium">{{ $item->occurred_on->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="text-stone-500">Ville</dt>
                    <dd class="font-medium">{{ $item->city }}</dd>
                </div>
                @if ($item->location)
                    <div>
                        <dt class="text-stone-500">Lieu</dt>
                        <dd class="font-medium">{{ $item->location }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-stone-500">Publié par</dt>
                    <dd class="font-medium">{{ $item->contact_name }}</dd>
                </div>
            </dl>

            @if ($item->description)
                <p class="mt-6 whitespace-pre-line text-stone-700">{{ $item->description }}</p>
            @endif
        </article>

        <aside class="lg:col-span-2">
            @if ($item->isResolved())
                <div class="card text-center">
                    <p class="font-medium">Cet objet a été rendu à son propriétaire 🎉</p>
                </div>
            @else
                <form method="POST" action="{{ route('items.messages.store', $item) }}" class="card space-y-4">
                    @csrf
                    <h2 class="text-lg font-semibold">
                        {{ $item->type === \App\Enums\ItemType::Lost ? 'Vous avez retrouvé cet objet ?' : 'C\'est à vous ?' }}
                    </h2>
                    <p class="text-sm text-stone-600">Votre message sera transmis à {{ $item->contact_name }}, qui vous répondra par e-mail.</p>

                    <div>
                        <label for="sender_name" class="label">Votre nom</label>
                        <input id="sender_name" name="sender_name" type="text" value="{{ old('sender_name') }}" maxlength="80" required class="input" autocomplete="name">
                        <x-field-error name="sender_name" />
                    </div>

                    <div>
                        <label for="sender_email" class="label">Votre e-mail</label>
                        <input id="sender_email" name="sender_email" type="email" value="{{ old('sender_email') }}" maxlength="160" required class="input" autocomplete="email">
                        <x-field-error name="sender_email" />
                    </div>

                    <div>
                        <label for="body" class="label">Message</label>
                        <textarea id="body" name="body" rows="5" minlength="10" maxlength="2000" required class="input"
                            placeholder="Donnez un détail que seul le propriétaire peut connaître.">{{ old('body') }}</textarea>
                        <x-field-error name="body" />
                    </div>

                    <div class="hidden" aria-hidden="true">
                        <label for="website">Site web</label>
                        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <button type="submit" class="btn-primary w-full">Envoyer le message</button>
                </form>
            @endif

            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <p class="font-semibold">Conseils de sécurité</p>
                <ul class="mt-1 list-disc space-y-1 pl-5">
                    <li>Ne payez jamais pour récupérer un objet.</li>
                    <li>Donnez rendez-vous dans un lieu public (commissariat, mairie…).</li>
                    <li>Vérifiez l'identité du propriétaire avant de rendre un document.</li>
                </ul>
            </div>
        </aside>
    </div>
</x-layout>
