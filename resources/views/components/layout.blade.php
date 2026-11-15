@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-50 font-sans text-stone-900 antialiased">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4">
                <a href="{{ route('items.index') }}" class="flex items-center gap-2 text-lg font-semibold">
                    <span class="inline-flex size-8 items-center justify-center rounded-lg bg-amber-500 text-stone-950" aria-hidden="true">🔎</span>
                    {{ config('app.name') }}
                </a>

                <nav class="flex items-center gap-2">
                    <a href="{{ route('items.create', ['type' => 'lost']) }}" class="btn-secondary hidden sm:inline-flex">J'ai perdu</a>
                    <a href="{{ route('items.create', ['type' => 'found']) }}" class="btn-primary">J'ai trouvé</a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-8">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="mx-auto max-w-5xl px-4 pb-10 text-xs text-stone-500">
            Ne publiez jamais le numéro d'un document et ne payez jamais pour récupérer un objet.
        </footer>
    </body>
</html>
