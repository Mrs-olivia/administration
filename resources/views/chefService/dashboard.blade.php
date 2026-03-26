<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tableau de bord — Chef de service
            </h2>
            <a href="{{ route('chefService.forms.index') }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-950 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Liste des dossiers
            </a>
        </div>
    </x-slot>

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl space-y-6 px-4 lg:px-8">
            <div class="flex flex-wrap items-center gap-3 rounded-lg border border-dashed border-amber-300 bg-amber-50/80 p-4 dark:border-amber-800 dark:bg-amber-950/20">
                <p class="text-sm text-gray-700 dark:text-gray-300">Traitement des dossiers : liste, validation, notes.</p>
                <a href="{{ route('chefService.forms.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-sm font-medium text-amber-950 shadow ring-1 ring-amber-200 hover:bg-amber-100 dark:bg-gray-800 dark:text-amber-200 dark:ring-amber-700">
                    Ouvrir les formulaires chef de service
                </a>
            </div>

            {{-- Carte priorité --}}
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-amber-200 bg-gradient-to-br from-amber-950 to-amber-900 p-6 text-white shadow-lg dark:border-amber-900">
                    <p class="text-sm font-medium text-amber-100">À traiter (attente + en cours)</p>
                    <p class="mt-1 text-4xl font-bold tracking-tight">{{ $aTraiter }}</p>
                    <p class="mt-2 text-sm text-amber-200">Dossiers nécessitant une action ou un suivi.</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Traités</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $traitesCount }}</p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Décisions validées.</p>
                </div>
            </div>

            {{-- Synthèse statuts --}}
            <div>
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Synthèse</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach($statusLabels as $code => $label)
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $counts[$code] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Activité récente --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Dossiers récents</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">En attente, traités ou rejetés — les derniers mouvements.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Référence</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Objet</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Statut</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($recent as $f)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $f->reference }}</td>
                                    <td class="max-w-xs truncate px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($f->objet, 48) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-600 dark:text-gray-100">
                                            {{ $statusLabels[$f->status] ?? $f->status }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                        <a href="{{ route('chefService.forms.show', $f->id) }}" class="font-medium text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Ouvrir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Aucun dossier à afficher.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
