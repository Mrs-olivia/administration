<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dossier — {{ $formulaire->reference }}
            </h2>
            <a href="{{ route('chefService.forms.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                Retour à la liste
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="p-4 text-sm rounded-lg bg-green-100 text-green-800 border border-green-300">{{ session('success') }}</div>
        </div>
    @endif
    @if (session('info'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="p-4 text-sm rounded-lg bg-blue-50 text-blue-900 border border-blue-200">{{ session('info') }}</div>
        </div>
    @endif

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Un seul enregistrement en base : vos actions mettent à jour <strong>le même statut</strong> et <strong>le même champ commentaire</strong> que ceux vus par le secrétariat.</p>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-950 to-blue-800">
                    <h3 class="text-2xl font-bold text-white">{{ $formulaire->reference }}</h3>
                    <p class="text-blue-100 text-sm mt-1">
                        <span class="text-blue-200/90">Service initiateur :</span>
                        <span class="font-mono font-semibold">{{ $formulaire->initiatingServiceCode() }}</span>
                        <span class="text-blue-200/80">— {{ $formulaire->initiatingServiceLabel() }}</span>
                    </p>
                    @if(filled($formulaire->origine_reference))
                        <p class="text-blue-200 text-sm mt-2 border-t border-blue-700/50 pt-2">
                            Dossier d’origine : <span class="font-mono font-semibold">{{ $formulaire->origine_reference }}</span>
                            @if(filled($formulaire->origine_service_code))
                                <span class="text-blue-100/80">({{ $formulaire->origineServiceLabel() }})</span>
                            @endif
                        </p>
                    @endif
                    <p class="text-blue-100 mt-1">Créé le {{ $formulaire->created_at->format('d/m/Y à H:i') }}</p>
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
                                <span class="inline-flex text-sm font-medium px-3 py-1 rounded
                                    @if($formulaire->status === \App\Models\Formulaire::STATUS_EN_ATTENTE) bg-yellow-100 text-yellow-800
                                    @elseif($formulaire->status === \App\Models\Formulaire::STATUS_EN_COURS) bg-blue-100 text-blue-800
                                    @elseif($formulaire->status === \App\Models\Formulaire::STATUS_TRAITE) bg-green-100 text-green-800
                                    @elseif($formulaire->status === \App\Models\Formulaire::STATUS_REJETE) bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $formulaire->statusLabel() }}
                                </span>
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

                    @if($formulaire->annotation_chef)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-600 p-4 bg-gray-50 dark:bg-gray-900/50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Journal des annotations <span class="font-normal text-gray-500 dark:text-gray-400">([code service | date] — type — texte)</span></span>
                            <p class="mt-2 text-gray-900 dark:text-white whitespace-pre-wrap font-mono text-sm">{{ $formulaire->annotation_chef }}</p>
                        </div>
                    @endif
                </div>

                @can('agirCommeChef', $formulaire)
                    <div class="px-6 py-5 border-t border-gray-200 dark:border-gray-600 space-y-6 bg-gray-50 dark:bg-gray-900/30">
                        <h4 class="font-semibold text-gray-900 dark:text-white">Actions chef</h4>

                        <form action="{{ route('chefService.forms.annoter', $formulaire) }}" method="POST" class="space-y-2">
                            @csrf
                            <label for="annotation_annoter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ajouter une annotation (facultatif)</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Chaque saisie est ajoutée au journal avec le préfixe <strong class="font-mono">[{{ auth()->user()?->service_code ?? $formulaire->service_code }} | date]</strong> pour distinguer les services.</p>
                            <textarea id="annotation_annoter" name="annotation" rows="3"
                                placeholder="Nouvelle note (le journal complet reste affiché ci-dessus)…"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">{{ old('annotation') }}</textarea>
                            @error('annotation')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm">
                                Enregistrer l’annotation
                            </button>
                        </form>

                        <form action="{{ route('chefService.forms.valider', $formulaire) }}" method="POST" class="space-y-2 border-t border-gray-200 dark:border-gray-600 pt-6">
                            @csrf
                            <label for="commentaire_valider" class="block text-sm font-medium text-green-800 dark:text-green-300">Valider le dossier</label>
                            @if($formulaire->hasChefAnnotation())
                                <p class="text-xs text-gray-600 dark:text-gray-400">Commentaire <strong>optionnel</strong> : si vous laissez vide, l’annotation déjà enregistrée ci-dessus sera conservée comme décision.</p>
                                <textarea id="commentaire_valider" name="commentaire" rows="3"
                                    placeholder="Précision ou commentaire final (optionnel)…"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">{{ old('commentaire') }}</textarea>
                            @else
                                <p class="text-xs text-amber-700 dark:text-amber-300">Un <strong>commentaire est obligatoire</strong> pour valider tant qu’aucune annotation n’a été enregistrée.</p>
                                <textarea id="commentaire_valider" name="commentaire" rows="3" required
                                    placeholder="Décision et commentaire final…"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">{{ old('commentaire') }}</textarea>
                            @endif
                            @error('commentaire')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-lg text-sm">
                                Valider le dossier
                            </button>
                        </form>

                        <form action="{{ route('chefService.forms.rejeter', $formulaire) }}" method="POST" class="space-y-2 border-t border-gray-200 dark:border-gray-600 pt-6">
                            @csrf
                            <label for="commentaire_rejeter" class="block text-sm font-medium text-red-800 dark:text-red-300">Rejeter le dossier</label>
                            @if($formulaire->hasChefAnnotation())
                                <p class="text-xs text-gray-600 dark:text-gray-400">Motif <strong>optionnel</strong> si une annotation existe déjà ; sinon saisissez le motif de rejet.</p>
                                <textarea id="commentaire_rejeter" name="commentaire" rows="3"
                                    placeholder="Motif du rejet (optionnel si annotation déjà présente)…"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">{{ old('commentaire') }}</textarea>
                            @else
                                <p class="text-xs text-red-700 dark:text-red-300">Le <strong>motif (commentaire) est obligatoire</strong> pour rejeter.</p>
                                <textarea id="commentaire_rejeter" name="commentaire" rows="3" required
                                    placeholder="Motif du rejet…"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm">{{ old('commentaire') }}</textarea>
                            @endif
                            @error('commentaire')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg text-sm">
                                Rejeter le dossier
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    </section>
</x-app-layout>
