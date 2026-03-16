<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestion des documents administratifs
        </h2>
    </x-slot>

    @if (session('success'))
        <div class="flex p-4 mb-4 text-sm rounded-lg bg-green-100 text-green-700 border border-green-400" id="success-message">
            {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('success-message').style.display = 'none';
            }, 5000);
        </script>
    @endif

    <section id="pageWrapper" class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 transition-all duration-300">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">

                <!-- Barre de recherche et actions -->
                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                    <div class="w-full md:w-1/2">
                        <form class="flex items-center">
                            <label for="simple-search" class="sr-only">Rechercher</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewbox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <input type="text" id="simple-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Rechercher" required="">
                            </div>
                        </form>
                    </div>

                    <div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                        <button id="filterDropdownButton" data-dropdown-toggle="filterDropdown" class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" type="button">
                            <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-4 w-4 mr-2 text-gray-400" viewbox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                            </svg>
                            Filtrer par statut
                            <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                            </svg>
                        </button>

                        <div id="filterDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                            <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Statut du document</h6>
                            <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
                                <li class="flex items-center"><a href="#" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">En attente</a></li>
                                <li class="flex items-center"><a href="#" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">En cours</a></li>
                                <li class="flex items-center"><a href="#" class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">Archivé</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tableau des formulaires -->
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
                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $formulaire->reference }}</th>
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

                                    <!-- Actions pour chef de service -->
                                    <td class="px-4 py-3 text-right">
                                        <button id="actions-{{ $formulaire->id }}-button" data-dropdown-toggle="actions-{{ $formulaire->id }}-dropdown" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                                            <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            </svg>
                                        </button>

                                        <div id="actions-{{ $formulaire->id }}-dropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                                            <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="actions-{{ $formulaire->id }}-button">
                                                <li>
                                                    <form action="{{ route('chefService.forms.approve', $formulaire) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="block w-full text-left py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Approuver</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a href="{{ route('chefService.forms.show', $formulaire) }}"
                                                        class="block py-2 px-4 text-left
                                                         hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">voir</a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('chefService.forms.cancel', $formulaire) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="block w-full text-left py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Annuler</button>
                                                    </form>
                                                </li>
                                            </ul>

    
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-4 text-center">Aucun formulaire trouvé.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (optionnelle) -->
                <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                        Showing <span class="font-semibold text-gray-900 dark:text-white">1-10</span> of <span class="font-semibold text-gray-900 dark:text-white">1000</span>
                    </span>
                    <ul class="inline-flex items-stretch -space-x-px">
                        <li><a href="#" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"><span class="sr-only">Previous</span> &lt; </a></li>
                        <li><a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">1</a></li>
                        <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400">...</span></li>
                        <li><a href="#" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">100</a></li>
                        <li><a href="#" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"><span class="sr-only">Next</span> &gt; </a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>
</x-app-layout>