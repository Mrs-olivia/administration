<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Détails du formulaire
            </h2>
            <a href="{{ route('secretaire.forms.indexs') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour
            </a>
        </div>
    </x-slot>

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 transition-all duration-300">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <!-- En-tête du formulaire -->
                <div class="px-6 py-4 bg-gradient-to-r from-blue-950 to-blue-800 dark:from-blue-900 dark:to-blue-700">
                    <h3 class="text-2xl font-bold text-white">{{ $formulaire->reference }}</h3>
                    <p class="text-blue-100 mt-1">Créé le {{ $formulaire->created_at->format('d/m/Y à H:i') }}</p>
                </div>

                <!-- Contenu détaillé -->
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Expéditeur -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expéditeur</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $formulaire->expediteur }}</p>
                        </div>

                        <!-- Type de document -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type de document</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $formulaire->type_document }}</p>
                        </div>

                        <!-- Date de réception -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date de réception</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $formulaire->date_reception?->format('d/m/Y') ?? '-' }}</p>
                        </div>

                        <!-- Date d'échéance -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date d'échéance</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $formulaire->date_echeance?->format('d/m/Y') ?? '-' }}</p>
                        </div>

                        <!-- Code de service -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code de service</label>
                            <p class="mt-1 text-lg text-gray-900 dark:text-white">{{ $formulaire->service_code }}</p>
                        </div>

                        <!-- Statut -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                            <div class="mt-1">
                                @if($formulaire->status == 0)
                                    <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded">En attente</span>
                                @elseif($formulaire->status == 1)
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded">En cours</span>
                                @elseif($formulaire->status == 2)
                                    <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded">Traité</span>
                                @elseif($formulaire->status == 3)
                                    <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded">Rejeté</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 text-sm font-medium px-3 py-1 rounded">Archivé</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Objet -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Objet</label>
                        <p class="mt-1 text-gray-900 dark:text-white whitespace-pre-wrap text-base">{{ $formulaire->objet }}</p>
                    </div>

                    <!-- Fichier -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fichier joint</label>
                        @if($formulaire->fichier)
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $formulaire->fichier) }}" download class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 16.5a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                        <path d="M3 4a2 2 0 012-2h6a1 1 0 00-.757-1.707A2 2 0 004 4v12a2 2 0 012 2h6a2 2 0 012-2V4a1 1 0 00-1.243-1.06A2 2 0 0010 2H4a2 2 0 00-2 2v9a2 2 0 002 2h.5a1 1 0 01.82.388l2.5 3.082A1 1 0 009 17H4a2 2 0 01-1-3.75V4z"></path>
                                    </svg>
                                    Télécharger le fichier
                                </a>
                            </div>
                        @else
                            <p class="mt-2 text-gray-500 dark:text-gray-400">Aucun fichier joint</p>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600 flex gap-3 flex-wrap">
                    <a href="{{ route('secretaire') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Retour à la liste
                    </a>

                    <a href="{{ route('formulaires.edit', $formulaire) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Modifier
                    </a>

                    <form action="{{ route('formulaires.destroy', $formulaire) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formulaire ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
