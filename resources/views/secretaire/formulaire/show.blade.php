<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dossier — {{ $formulaire->reference }}
            </h2>
            <a href="{{ route('secretaire.forms.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition text-sm">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="p-4 text-sm rounded-lg bg-green-100 text-green-800 border border-green-300">{{ session('success') }}</div>
        </div>
    @endif

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">Source unique : ce dossier est le même en base pour tout le monde. Le statut et le commentaire chef se mettent à jour automatiquement ci-dessous (rafraîchissement ~5 s).</p>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden"
                 x-data="secretaireDossierPollShow({
                    pollUrl: '{{ route('secretaire.forms.pollShow', $formulaire) }}',
                    initialStatusLabel: @js($formulaire->statusLabel()),
                    initialAnnotation: @js($formulaire->annotation_chef),
                    initialVariant: @js($formulaire->statusBadgeVariant()),
                 })">

                <div class="px-6 py-4 bg-gradient-to-r from-blue-950 to-blue-800">
                    <h3 class="text-2xl font-bold text-white">{{ $formulaire->reference }}</h3>
                    <p class="text-blue-100 mt-1">Créé le {{ $formulaire->created_at->format('d/m/Y à H:i') }}</p>
                    @if($formulaire->sent_to_chef_at)
                        <p class="text-blue-200 text-sm mt-1">Transmis au chef le {{ $formulaire->sent_to_chef_at->format('d/m/Y à H:i') }}</p>
                    @endif
                </div>

                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Expéditeur</span>
                            <p class="text-lg text-gray-900 dark:text-white">{{ $formulaire->expediteur }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Type de document</span>
                            <p class="text-lg text-gray-900 dark:text-white">{{ $formulaire->type_document }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Date de réception</span>
                            <p class="text-lg text-gray-900 dark:text-white">{{ $formulaire->date_reception?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Date d'échéance</span>
                            <p class="text-lg text-gray-900 dark:text-white">{{ $formulaire->date_echeance?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Code service</span>
                            <p class="text-lg text-gray-900 dark:text-white">{{ $formulaire->service_code }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Statut</span>
                            <div class="mt-1">
                                <span x-text="statusLabel" x-bind:class="badgeClass()"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Objet</span>
                        <p class="mt-1 text-gray-900 dark:text-white whitespace-pre-wrap">{{ $formulaire->objet }}</p>
                    </div>

                    @if($formulaire->fichier)
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Fichier</span>
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $formulaire->fichier) }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">
                                    Télécharger / ouvrir le fichier
                                </a>
                            </div>
                        </div>
                    @endif

                    <div x-cloak class="space-y-2">
                        <div x-show="annotation && annotation.length" class="rounded-lg border border-amber-200 dark:border-amber-900 p-4 bg-amber-50/80 dark:bg-amber-950/20">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Décision / commentaire du chef</span>
                            <p class="mt-2 text-gray-900 dark:text-white whitespace-pre-wrap" x-text="annotation"></p>
                        </div>
                        <div x-show="!annotation || !annotation.length" class="rounded-lg border border-dashed border-gray-300 dark:border-gray-600 p-4 text-sm text-gray-500 dark:text-gray-400">
                            Aucun commentaire chef pour l’instant.
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 dark:text-gray-500" x-show="lastSync">
                        Dernière synchro. affichée : <span x-text="lastSync ? lastSync.toLocaleTimeString() : ''"></span>
                    </p>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-600 flex flex-wrap gap-2">
                    <a href="{{ route('secretaire.forms.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm">Liste</a>

                    @can('update', $formulaire)
                        <a href="{{ route('secretaire.forms.edit', $formulaire) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm">Éditer</a>
                    @endcan

                    @can('send', $formulaire)
                        <form action="{{ route('secretaire.forms.send', $formulaire) }}" method="POST" class="inline" onsubmit="return confirm('Notifier le chef ?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm">Envoyer au chef</button>
                        </form>
                    @endcan

                    @can('delete', $formulaire)
                        <form action="{{ route('secretaire.forms.destroy', $formulaire) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">Supprimer</button>
                        </form>
                    @endcan

                    @can('archive', $formulaire)
                        <form action="{{ route('secretaire.forms.archive', $formulaire) }}" method="POST" class="inline" onsubmit="return confirm('Archiver ce dossier ?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm">Archiver</button>
                        </form>
                    @else
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500" title="Vous n’avez pas l’autorisation d’archiver ce dossier">Archiver</span>
                    @endcan
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
