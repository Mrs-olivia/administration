<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Gestion des documents
            administratives</h2>
    </x-slot>
    @if (session('success'))
        <div class="flex p-4 mb-4 text-sm rounded-lg bg-green-100 text-green-700 border border-green-400" id="success-message">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                var el = document.getElementById('success-message');
                if (el) el.style.display = 'none';
            }, 5000);
        </script>
    @endif
    @if (session('error'))
        <div class="flex p-4 mb-4 text-sm rounded-lg bg-red-100 text-red-800 border border-red-400">
            {{ session('error') }}
        </div>
    @endif

    <section id="pageWrapper" class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 transition-all duration-300">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <p class="mb-3 text-xs text-gray-500 dark:text-gray-400">Les dossiers partagent une <strong>source unique</strong> en base : le statut et le commentaire chef sont les mêmes pour le secrétariat et le chef. Actualisation automatique ci-dessous.</p>
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden"
                 x-data="secretaireDossierPollBatch({ pollUrl: '{{ route('secretaire.forms.poll') }}', ids: @json($formulaires->pluck('id')->values()) })">
                <div
                    class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">
                        <form class="flex items-center">
                            <label for="simple-search" class="sr-only">Rechercher</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                        fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" id="simple-search"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Rechercher" required="">
                            </div>
                        </form>
                    </div>
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <button id="openModalButton" type="button"
                            class="flex items-center justify-center text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none dark:bg-blue-800 dark:hover:bg-blue-700 dark:focus:ring-blue-900 transition-colors">
                            <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Nouveau
                        </button>

                        <div class="flex items-center space-x-3 w-full md:w-auto">

                            @php
                                $activeStatus = request()->query('status');
                                $activeStatusLabel = $activeStatus !== null
                                    ? match ((int) $activeStatus) {
                                        \App\Models\Formulaire::STATUS_EN_ATTENTE => 'Nouveau',
                                        \App\Models\Formulaire::STATUS_EN_COURS => 'En cours',
                                        \App\Models\Formulaire::STATUS_ARCHIVE => 'Archivé',
                                        default => '',
                                    }
                                    : '';
                            @endphp

                            <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown"
                                class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                                type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true"
                                    class="h-4 w-4 mr-2 text-gray-400" viewbox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $activeStatusLabel ?: 'Filtrer par statut' }}
                                <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path clip-rule="evenodd" fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </button>
                            <div id="filterDropdown"
                                class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Statut du document
                                </h6>
                                <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                                    <li class="flex items-center">
                                        <a href="{{ route('secretaire.forms.index', array_merge(request()->except(['status', 'page']), ['status' => \App\Models\Formulaire::STATUS_EN_ATTENTE])) }}"
                                            class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 @if($activeStatus !== null && (int) $activeStatus === \App\Models\Formulaire::STATUS_EN_ATTENTE) font-semibold text-blue-800 dark:text-blue-300 @endif">
                                            Nouveau
                                        </a>
                                    </li>

                                    <li class="flex items-center">
                                        <a href="{{ route('secretaire.forms.index', array_merge(request()->except(['status', 'page']), ['status' => \App\Models\Formulaire::STATUS_EN_COURS])) }}"
                                            class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 @if($activeStatus !== null && (int) $activeStatus === \App\Models\Formulaire::STATUS_EN_COURS) font-semibold text-blue-800 dark:text-blue-300 @endif">
                                            En cours
                                        </a>
                                    </li>

                                    <li class="flex items-center">
                                        <a href="{{ route('secretaire.forms.index', array_merge(request()->except(['status', 'page']), ['status' => \App\Models\Formulaire::STATUS_ARCHIVE])) }}"
                                            class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 @if($activeStatus !== null && (int) $activeStatus === \App\Models\Formulaire::STATUS_ARCHIVE) font-semibold text-blue-800 dark:text-blue-300 @endif">
                                            Archivé
                                        </a>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Référence</th>
                                <th scope="col" class="px-4 py-3">expediteur</th>
                                <th scope="col" class="px-4 py-3">objet</th>
                                <th scope="col" class="px-4 py-3">type_document</th>
                                <th scope="col" class="px-4 py-3">date_reception</th>
                                <th scope="col" class="px-4 py-3">date_echeance</th>
                                <th scope="col" class="px-4 py-3">Fichier</th>
                                <th scope="col" class="px-4 py-3">Statut</th>

                                <th scope="col" class="px-4 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formulaires as $formulaire)
                                <tr class="border-b dark:border-gray-700">
                                    <th scope="row"
                                        class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $formulaire->reference }}</th>
                                    <td class="px-4 py-3">{{ $formulaire->expediteur }}</td>
                                    <td class="px-4 py-3">{{ $formulaire->objet }}</td>
                                    <td class="px-4 py-3">{{ $formulaire->type_document }}</td>
                                    <td class="px-4 py-3">{{ $formulaire->date_reception?->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3">{{ $formulaire->date_echeance?->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3">
                                        @if($formulaire->fichier)
                                            <a href="{{ asset('storage/' . $formulaire->fichier) }}" download class="text-blue-600 hover:text-blue-800 underline">
                                                📄 Télécharger
                                            </a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span data-secretairepoll-status="{{ $formulaire->id }}"
                                            class="inline-flex px-2.5 py-0.5 rounded text-xs font-medium
                                            @if($formulaire->status === \App\Models\Formulaire::STATUS_EN_ATTENTE) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200
                                            @elseif($formulaire->status === \App\Models\Formulaire::STATUS_EN_COURS) bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200
                                            @elseif($formulaire->status === \App\Models\Formulaire::STATUS_TRAITE) bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200
                                            @elseif($formulaire->status === \App\Models\Formulaire::STATUS_REJETE) bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100 @endif">
                                            {{ $formulaire->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1 justify-end max-w-xs ms-auto">
                                            <a href="{{ route('secretaire.forms.show', $formulaire) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200">Voir</a>
                                            @can('update', $formulaire)
                                                <a href="{{ route('secretaire.forms.edit', $formulaire) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-blue-100 text-blue-800 hover:bg-blue-200">Éditer</a>
                                            @endcan
                                            @can('send', $formulaire)
                                                <form action="{{ route('secretaire.forms.send', $formulaire) }}" method="POST" class="inline"
                                                      onsubmit="return confirm({{ $formulaire->sent_to_chef_at ? "'Voulez-vous renvoyer une notification au chef ?'" : "'Notifier le chef pour ce dossier ?'" }});">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-amber-100 text-amber-900 hover:bg-amber-200">
                                                        {{ $formulaire->sent_to_chef_at ? 'Renvoyer' : 'Envoyer' }}
                                                    </button>
                                                </form>
                                            @endcan
                                            @can('delete', $formulaire)
                                                <form action="{{ route('secretaire.forms.destroy', $formulaire) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement ce dossier ? Cette action est irréversible.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800 hover:bg-red-200">Supprimer</button>
                                                </form>
                                            @endcan
                                            @can('archive', $formulaire)
                                                <form action="{{ route('secretaire.forms.archive', $formulaire) }}" method="POST" class="inline" onsubmit="return confirm('Archiver ce dossier ?');">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-purple-100 text-purple-800 hover:bg-purple-200">Archiver</button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-400 dark:bg-gray-600 dark:text-gray-500 cursor-not-allowed" title="Vous n’avez pas l’autorisation d’archiver ce dossier">Archiver</span>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-4 text-center">Aucun dossier trouvé.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $formulaires->links() }}
                </div>
            </div>
        </div>

        <!-- Modal component -->
        <x-formulaire-modal />

    </section>
</x-app-layout>
