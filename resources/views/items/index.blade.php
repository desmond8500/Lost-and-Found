<x-layout>
    <section class="mb-8">
        <h1 class="text-3xl font-semibold tracking-tight">Objets perdus et trouvés</h1>
        <p class="mt-2 max-w-2xl text-stone-600">
            Vous avez perdu votre carte d'identité, votre passeport ou vos clés ? Cherchez votre nom ci-dessous.
            Vous avez trouvé un objet ? Publiez-le pour que son propriétaire puisse vous contacter.
        </p>
    </section>

    <form method="GET" action="{{ route('items.index') }}" class="card mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5" role="search">
        <div class="sm:col-span-2">
            <label for="q" class="label">Nom ou mot-clé</label>
            <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="Ex. : Awa Ndiaye, clés Peugeot…" class="input">
        </div>

        <div>
            <label for="type" class="label">Type</label>
            <select id="type" name="type" class="input">
                <option value="">Tous</option>
                @foreach (\App\Enums\ItemType::cases() as $type)
                    <option value="{{ $type->value }}" @selected(($filters['type'] ?? null) === $type->value)>{{ $type->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="category" class="label">Catégorie</label>
            <select id="category" name="category" class="input">
                <option value="">Toutes</option>
                @foreach (\App\Enums\ItemCategory::cases() as $category)
                    <option value="{{ $category->value }}" @selected(($filters['category'] ?? null) === $category->value)>{{ $category->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="city" class="label">Ville</label>
            <input id="city" name="city" type="text" value="{{ $filters['city'] ?? '' }}" placeholder="Ex. : Dakar" class="input">
        </div>

        <div class="flex items-center gap-3 sm:col-span-2 lg:col-span-5">
            <button type="submit" class="btn-primary">Rechercher</button>
            @if (array_filter($filters))
                <a href="{{ route('items.index') }}" class="text-sm text-stone-600 underline hover:text-stone-900">Effacer les filtres</a>
            @endif
        </div>
    </form>

    @if ($items->isEmpty())
        <div class="card text-center">
            <p class="font-medium">Aucune annonce ne correspond à votre recherche.</p>
            <p class="mt-1 text-sm text-stone-600">Vous pouvez publier une annonce pour être contacté si quelqu'un retrouve votre objet.</p>
            <a href="{{ route('items.create', ['type' => 'lost']) }}" class="btn-primary mt-4">Déclarer un objet perdu</a>
        </div>
    @else
        <p class="mb-4 text-sm text-stone-600">{{ $items->total() }} annonce(s)</p>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
                <x-item-card :item="$item" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $items->links() }}
        </div>
    @endif
</x-layout>
