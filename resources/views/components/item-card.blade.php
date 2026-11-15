@props(['item'])

<a href="{{ route('items.show', $item) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xs transition hover:border-amber-400 hover:shadow-md">
    @if ($item->photoUrl())
        <img src="{{ $item->photoUrl() }}" alt="" class="h-40 w-full object-cover" loading="lazy">
    @else
        <div class="flex h-40 items-center justify-center bg-stone-100 text-sm text-stone-400">
            {{ $item->category->label() }}
        </div>
    @endif

    <div class="flex flex-1 flex-col gap-2 p-4">
        <div class="flex items-center gap-2 text-xs">
            <span class="rounded-full px-2 py-0.5 font-semibold {{ $item->type->badgeClasses() }}">{{ $item->type->label() }}</span>
            <span class="text-stone-500">{{ $item->category->label() }}</span>
        </div>

        <h3 class="font-semibold group-hover:text-amber-700">{{ $item->title }}</h3>

        @if ($item->name_on_item)
            <p class="text-sm text-stone-700">Au nom de <strong>{{ $item->name_on_item }}</strong></p>
        @endif

        <p class="mt-auto text-xs text-stone-500">
            {{ $item->city }} · le {{ $item->occurred_on->format('d/m/Y') }}
        </p>
    </div>
</a>
