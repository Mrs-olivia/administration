<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tableau de bord — Secrétariat
            </h2>
            <a href="{{ route('secretaire.forms.index') }}"
               class="inline-flex items-center justify-center rounded-lg bg-blue-950 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Gérer les formulaires
            </a>
        </div>
    </x-slot>

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl space-y-6 px-4 lg:px-8">
            <div class="flex flex-wrap items-center gap-3 rounded-lg border border-dashed border-blue-300 bg-blue-50/80 p-4 dark:border-blue-800 dark:bg-blue-950/20">
                <p class="text-sm text-gray-700 dark:text-gray-300">Accès rapide aux formulaires (saisie, liste, modification).</p>
                <a href="{{ route('secretaire.forms.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-sm font-medium text-blue-950 shadow ring-1 ring-blue-200 hover:bg-blue-100 dark:bg-gray-800 dark:text-blue-200 dark:ring-blue-700">
                    Ouvrir la page formulaires
                </a>
            </div>

            {{-- Carte total --}}
            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-blue-950 to-blue-900 p-6 text-white shadow-lg dark:border-blue-900">
                <p class="text-sm font-medium text-blue-100">Documents enregistrés</p>
                <p class="mt-1 text-4xl font-bold tracking-tight">{{ $total }}</p>
                <p class="mt-2 text-sm text-blue-200">Vue d’ensemble de l’activité du secrétariat.</p>
            </div>

            {{-- Répartition par statut --}}
            <div>
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Par statut</h3>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach($statusLabels as $code => $label)
                        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
                            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $counts[$code] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Derniers dossiers (SSOT — statut synchronisé avec la base) --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
                 x-data="secretaireDossierPollBatch({ pollUrl: '{{ route('secretaire.forms.poll') }}', ids: @json($recent->pluck('id')->values()) })">
                <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Derniers dossiers</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Les plus récemment créés ou mis à jour — statut actualisé automatiquement.</p>
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
                                        <span data-secretairepoll-status="{{ $f->id }}"
                                            class="inline-flex px-2.5 py-0.5 rounded text-xs font-medium
                                            @if($f->status === \App\Models\Formulaire::STATUS_EN_ATTENTE) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200
                                            @elseif($f->status === \App\Models\Formulaire::STATUS_EN_COURS) bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200
                                            @elseif($f->status === \App\Models\Formulaire::STATUS_TRAITE) bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200
                                            @elseif($f->status === \App\Models\Formulaire::STATUS_REJETE) bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100 @endif">
                                            {{ $f->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                        <a href="{{ route('secretaire.forms.show', $f) }}" class="font-medium text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Ouvrir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Aucun formulaire pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
