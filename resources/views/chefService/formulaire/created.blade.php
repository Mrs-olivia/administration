<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Formulaires créés (chef de service)
        </h2>
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
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Ici vous retrouvez les formulaires <strong>créés par vous</strong> et transmis au secrétariat.
                        </p>
                    </div>

                    <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <button id="openChefModalButton" type="button"
                            class="flex items-center justify-center text-white bg-blue-950 hover:bg-blue-900 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 focus:outline-none dark:bg-blue-800 dark:hover:bg-blue-700 dark:focus:ring-blue-900 transition-colors">
                            <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path clip-rule="evenodd" fill-rule="evenodd"
                                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" />
                            </svg>
                            Créer + envoyer
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-4 py-3">Référence</th>
                                <th scope="col" class="px-4 py-3">Expéditeur</th>
                                <th scope="col" class="px-4 py-3">Objet</th>
                                <th scope="col" class="px-4 py-3">Type</th>
                                <th scope="col" class="px-4 py-3">Date réception</th>
                                <th scope="col" class="px-4 py-3">Date échéance</th>
                                <th scope="col" class="px-4 py-3">Fichier</th>
                                <th scope="col" class="px-4 py-3">Statut</th>
                                <th scope="col" class="px-4 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formulaires as $formulaire)
                                <tr class="border-b dark:border-gray-700">
                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $formulaire->reference }}
                                    </th>
                                    <td class="px-4 py-3">{{ $formulaire->expediteur }}</td>
                                    <td class="px-4 py-3">{{ $formulaire->objet }}</td>
                                    <td class="px-4 py-3">{{ $formulaire->type_document }}</td>
                                    <td class="px-4 py-3">{{ $formulaire->date_reception?->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3">{{ $formulaire->date_echeance?->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3">
                                        @if($formulaire->fichier)
                                            <a href="{{ asset('storage/' . $formulaire->fichier) }}" download class="text-blue-600 hover:text-blue-800 underline">📄 Télécharger</a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($formulaire->status == 0)
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">En attente</span>
                                        @elseif($formulaire->status == 1)
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">En cours</span>
                                        @elseif($formulaire->status == 2)
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Traité</span>
                                        @elseif($formulaire->status == 3)
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Rejeté</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Archivé</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('chefService.forms.show', $formulaire) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-950 text-white hover:bg-blue-900">
                                            Voir
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-4 text-center">Aucun formulaire créé.</td>
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

        <x-formulaire-modal-chef />
    </section>
</x-app-layout>

