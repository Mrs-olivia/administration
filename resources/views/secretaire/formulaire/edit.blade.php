<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Modifier le formulaire
            </h2>
            <a href="{{ route('secretaire.forms.show', $formulaire) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Annuler
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="p-4 text-sm rounded-lg bg-green-100 text-green-800 border border-green-300" role="status">{{ session('success') }}</div>
        </div>
    @endif

    <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 transition-all duration-300">
        <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-950 to-blue-800 dark:from-blue-900 dark:to-blue-700">
                    <h3 class="text-2xl font-bold text-white">{{ $formulaire->reference }}</h3>
                </div>

                <form method="POST" action="{{ route('secretaire.forms.update', $formulaire) }}" enctype="multipart/form-data" class="px-6 py-6">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-400">
                            <h4 class="font-bold mb-2">Erreurs de validation:</h4>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6">
                        <div class="rounded-lg border border-gray-200 dark:border-gray-600 p-4 bg-gray-50 dark:bg-gray-900/40">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Référence (non modifiable)</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $formulaire->reference }}</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $formulaire->serviceLabel() }} — année {{ $formulaire->annee }}, n° {{ $formulaire->numero_ordre }}</p>
                        </div>

                        <!-- Expéditeur -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expéditeur</label>
                            <input name="expediteur" type="text" required value="{{ old('expediteur', $formulaire->expediteur) }}"
                                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2" />
                            @error('expediteur')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Objet -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Objet</label>
                            <textarea name="objet" required rows="3"
                                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">{{ old('objet', $formulaire->objet) }}</textarea>
                            @error('objet')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date réception</label>
                                <input id="date_reception_edit" name="date_reception" type="date" value="{{ old('date_reception', $formulaire->date_reception?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2" />
                                @error('date_reception')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date échéance</label>
                                <input id="date_echeance_edit" name="date_echeance" type="date" value="{{ old('date_echeance', $formulaire->date_echeance?->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2" />
                                @error('date_echeance')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Type document, Fichier -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type document</label>
                                <select id="type_document_edit" name="type_document" required class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2">
                                    <option value="">-- Choisir --</option>
                                    <option value="Note de service" {{ old('type_document', $formulaire->type_document) == 'Note de service' ? 'selected' : '' }}>Note de service</option>
                                    <option value="Note de transmission" {{ old('type_document', $formulaire->type_document) == 'Note de transmission' ? 'selected' : '' }}>Note de transmission</option>
                                    <option value="Compte-rendu" {{ old('type_document', $formulaire->type_document) == 'Compte-rendu' ? 'selected' : '' }}>Compte-rendu</option>
                                    <option value="Rapport" {{ old('type_document', $formulaire->type_document) == 'Rapport' ? 'selected' : '' }}>Rapport</option>
                                    <option value="Lettre administrative" {{ old('type_document', $formulaire->type_document) == 'Lettre administrative' ? 'selected' : '' }}>Lettre administrative</option>
                                    <option value="Décision" {{ old('type_document', $formulaire->type_document) == 'Décision' ? 'selected' : '' }}>Décision</option>
                                    <option value="Arrêté" {{ old('type_document', $formulaire->type_document) == 'Arrêté' ? 'selected' : '' }}>Arrêté</option>
                                    <option value="Circulaire" {{ old('type_document', $formulaire->type_document) == 'Circulaire' ? 'selected' : '' }}>Circulaire</option>
                                    <option value="Registre" {{ old('type_document', $formulaire->type_document) == 'Registre' ? 'selected' : '' }}>Registre</option>
                                    <option value="Autre" {{ (old('type_document', $formulaire->type_document) && !in_array(old('type_document', $formulaire->type_document), ['Note de service', 'Note de transmission', 'Compte-rendu', 'Rapport', 'Lettre administrative', 'Décision', 'Arrêté', 'Circulaire', 'Registre'])) ? 'selected' : '' }}>Autre</option>
                                </select>
                                <input type="text" id="autre_type_document_edit" name="autre_type_document" placeholder="Précisez le type de document" 
                                    class="mt-2 hidden block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2" 
                                    value="{{ (old('type_document', $formulaire->type_document) && !in_array(old('type_document', $formulaire->type_document), ['Note de service', 'Note de transmission', 'Compte-rendu', 'Rapport', 'Lettre administrative', 'Décision', 'Arrêté', 'Circulaire', 'Registre'])) ? old('type_document', $formulaire->type_document) : old('autre_type_document') }}" />
                                @error('type_document')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fichier</label>
                                @if($formulaire->fichier)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Fichier actuel: <strong>{{ basename($formulaire->fichier) }}</strong></p>
                                @endif
                                <input name="fichier" type="file"
                                    class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Laisser vide pour conserver le fichier actuel</p>
                                @error('fichier')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="rounded-md bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-600 p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-300"><span class="font-medium">Statut actuel :</span> {{ $formulaire->statusLabel() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Le statut est géré par le workflow (chef / archivage). Vous ne pouvez pas le modifier manuellement.</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex justify-end gap-3">
                        <a href="{{ route('secretaire.forms.show', $formulaire) }}" class="inline-flex items-center px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        (function() {
            const typeDocumentSelect = document.getElementById('type_document_edit');
            const autreTypeDocumentInput = document.getElementById('autre_type_document_edit');
            const form = document.querySelector('form');
            const dateReceptionEdit = document.getElementById('date_reception_edit');
            const dateEcheanceEdit = document.getElementById('date_echeance_edit');
            const dateOrderMsg = 'Impossible d\'avoir une date d\'échéance inférieure à la date de réception.';

            const syncEcheanceMinEdit = () => {
                const r = dateReceptionEdit?.value;
                if (dateEcheanceEdit) {
                    dateEcheanceEdit.min = r || '';
                    if (r && dateEcheanceEdit.value && dateEcheanceEdit.value < r) {
                        dateEcheanceEdit.value = r;
                    }
                }
            };
            dateReceptionEdit?.addEventListener('change', syncEcheanceMinEdit);
            dateReceptionEdit?.addEventListener('input', syncEcheanceMinEdit);
            syncEcheanceMinEdit();

            // Gérer l'affichage/masquage du champ "Autre"
            const toggleAutreField = () => {
                if (typeDocumentSelect?.value === 'Autre') {
                    autreTypeDocumentInput?.classList.remove('hidden');
                    autreTypeDocumentInput?.setAttribute('required', 'required');
                } else {
                    autreTypeDocumentInput?.classList.add('hidden');
                    autreTypeDocumentInput?.removeAttribute('required');
                }
            };

            if (typeDocumentSelect) {
                typeDocumentSelect.addEventListener('change', toggleAutreField);
                // Initialiser au chargement de la page
                toggleAutreField();
            }

            // Gérer la soumission du formulaire
            form?.addEventListener('submit', function(e) {
                const r = dateReceptionEdit?.value;
                const ec = dateEcheanceEdit?.value;
                if (r && ec && ec < r) {
                    e.preventDefault();
                    alert(dateOrderMsg);
                    return;
                }
                if (typeDocumentSelect?.value === 'Autre') {
                    const autreValue = autreTypeDocumentInput?.value?.trim();
                    if (autreValue) {
                        // Remplacer la valeur du select par la valeur personnalisée
                        typeDocumentSelect.value = autreValue;
                    }
                }
            });
        })();
    </script>
</x-app-layout>
