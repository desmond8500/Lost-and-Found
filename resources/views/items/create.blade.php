<x-layout title="Publier une annonce">
    <h1 class="mb-2 text-2xl font-semibold tracking-tight">Publier une annonce</h1>
    <p class="mb-6 text-stone-600">Aucun compte n'est nécessaire : vous recevrez par e-mail un lien secret pour gérer votre annonce.</p>

    <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="card space-y-6">
        @csrf

        <fieldset>
            <legend class="label">Vous avez…</legend>
            <div class="grid grid-cols-2 gap-3">
                @foreach (\App\Enums\ItemType::cases() as $itemType)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-stone-300 px-4 py-3 has-checked:border-amber-500 has-checked:bg-amber-50">
                        <input type="radio" name="type" value="{{ $itemType->value }}" class="accent-amber-500"
                            @checked(old('type', $type->value) === $itemType->value)>
                        <span class="font-medium">{{ $itemType === \App\Enums\ItemType::Lost ? 'Perdu un objet' : 'Trouvé un objet' }}</span>
                    </label>
                @endforeach
            </div>
            <x-field-error name="type" />
        </fieldset>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="category" class="label">Catégorie</label>
                <select id="category" name="category" class="input" required>
                    @foreach (\App\Enums\ItemCategory::cases() as $category)
                        <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ $category->label() }}</option>
                    @endforeach
                </select>
                <x-field-error name="category" />
            </div>

            <div>
                <label for="title" class="label">Titre</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" maxlength="120" required
                    placeholder="Ex. : Carte d'identité trouvée près du marché" class="input">
                <x-field-error name="title" />
            </div>
        </div>

        <div>
            <label for="name_on_item" class="label">Nom inscrit sur l'objet <span class="font-normal text-stone-500">(facultatif)</span></label>
            <input id="name_on_item" name="name_on_item" type="text" value="{{ old('name_on_item') }}" maxlength="120"
                placeholder="Prénom et nom figurant sur la carte" class="input">
            <p class="mt-1 text-xs text-stone-500">Permet au propriétaire de se retrouver en cherchant son nom. N'indiquez <strong>jamais</strong> le numéro du document ni la date de naissance.</p>
            <x-field-error name="name_on_item" />
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label for="city" class="label">Ville</label>
                <input id="city" name="city" type="text" value="{{ old('city') }}" maxlength="80" required class="input">
                <x-field-error name="city" />
            </div>

            <div>
                <label for="location" class="label">Lieu précis <span class="font-normal text-stone-500">(facultatif)</span></label>
                <input id="location" name="location" type="text" value="{{ old('location') }}" maxlength="160"
                    placeholder="Quartier, rue, ligne de bus…" class="input">
                <x-field-error name="location" />
            </div>

            <div>
                <label for="occurred_on" class="label">Date</label>
                <input id="occurred_on" name="occurred_on" type="date" value="{{ old('occurred_on', now()->toDateString()) }}"
                    max="{{ now()->toDateString() }}" required class="input">
                <x-field-error name="occurred_on" />
            </div>
        </div>

        <div>
            <label for="description" class="label">Description <span class="font-normal text-stone-500">(facultatif)</span></label>
            <textarea id="description" name="description" rows="4" maxlength="2000" class="input"
                placeholder="Couleur, signes distinctifs, contenu…">{{ old('description') }}</textarea>
            <x-field-error name="description" />
        </div>

        <div>
            <label for="photo" class="label">Photo <span class="font-normal text-stone-500">(facultatif, 4 Mo max)</span></label>
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="input file:mr-3 file:rounded-md file:border-0 file:bg-stone-100 file:px-3 file:py-1">
            <p class="mt-1 text-xs text-stone-500">Pour un document d'identité, masquez le numéro, la photo et la signature avant de publier.</p>
            <x-field-error name="photo" />
        </div>

        <div class="grid gap-4 border-t border-stone-200 pt-6 sm:grid-cols-2">
            <div>
                <label for="contact_name" class="label">Votre prénom</label>
                <input id="contact_name" name="contact_name" type="text" value="{{ old('contact_name') }}" maxlength="80" required class="input" autocomplete="given-name">
                <x-field-error name="contact_name" />
            </div>

            <div>
                <label for="contact_email" class="label">Votre e-mail</label>
                <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email') }}" maxlength="160" required class="input" autocomplete="email">
                <p class="mt-1 text-xs text-stone-500">Jamais affiché publiquement. Les messages vous sont transmis par e-mail.</p>
                <x-field-error name="contact_email" />
            </div>
        </div>

        {{-- Piège à robots : invisible pour les humains --}}
        <div class="hidden" aria-hidden="true">
            <label for="website">Site web</label>
            <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Publier l'annonce</button>
        </div>
    </form>
</x-layout>
