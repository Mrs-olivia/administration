<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nouveau formulaire</h3>
            <button id="closeModalButton" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form method="POST" action="{{ route('secretaire.forms.store') }}" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-400">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Service Code</label>
                        <input name="service_code" type="text" required maxlength="10" value="{{ old('service_code') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Année</label>
                        <input name="annee" type="number" required min="2000" max="2100" value="{{ old('annee') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Numéro Ordre</label>
                        <input name="numero_ordre" type="number" required min="1" value="{{ old('numero_ordre') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Expéditeur</label>
                    <input name="expediteur" type="text" required value="{{ old('expediteur') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Objet</label>
                    <input name="objet" type="text" required value="{{ old('objet') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                </div>
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date réception</label>
                        <input name="date_reception" type="date" value="{{ old('date_reception') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date échéance</label>
                        <input name="date_echeance" type="date" value="{{ old('date_echeance') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type document</label>
                        <select id="type_document_modal" name="type_document" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">-- Choisir --</option>
                            <option value="Note de service" {{ old('type_document') == 'Note de service' ? 'selected' : '' }}>Note de service</option>
                            <option value="Note de transmission" {{ old('type_document') == 'Note de transmission' ? 'selected' : '' }}>Note de transmission</option>
                            <option value="Compte-rendu" {{ old('type_document') == 'Compte-rendu' ? 'selected' : '' }}>Compte-rendu</option>
                            <option value="Rapport" {{ old('type_document') == 'Rapport' ? 'selected' : '' }}>Rapport</option>
                            <option value="Lettre administrative" {{ old('type_document') == 'Lettre administrative' ? 'selected' : '' }}>Lettre administrative</option>
                            <option value="Décision" {{ old('type_document') == 'Décision' ? 'selected' : '' }}>Décision</option>
                            <option value="Arrêté" {{ old('type_document') == 'Arrêté' ? 'selected' : '' }}>Arrêté</option>
                            <option value="Circulaire" {{ old('type_document') == 'Circulaire' ? 'selected' : '' }}>Circulaire</option>
                            <option value="Registre" {{ old('type_document') == 'Registre' ? 'selected' : '' }}>Registre</option>
                            <option value="Autre" {{ old('type_document') == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        <input type="text" id="autre_type_document_modal" name="autre_type_document" placeholder="Précisez le type de document" 
                            class="mt-2 hidden block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('autre_type_document') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fichier</label>
                        <input name="fichier" type="file" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Statut</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>En attente</option>
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>En cours</option>
                            <option value="2" {{ old('status') == '2' ? 'selected' : '' }}>Traité</option>
                            <option value="3" {{ old('status') == '3' ? 'selected' : '' }}>Rejeté</option>
                            <option value="4" {{ old('status') == '4' ? 'selected' : '' }}>Archivé</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-200 rounded">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>

    (function() {
        const openModalButton = document.getElementById('openModalButton');
        const createModal = document.getElementById('createModal');
        const closeModalButton = document.getElementById('closeModalButton');
        const cancelModal = document.getElementById('cancelModal');
        const form = createModal?.querySelector('form');
        const typeDocumentSelect = document.getElementById('type_document_modal');
        const autreTypeDocumentInput = document.getElementById('autre_type_document_modal');

        if (!openModalButton || !createModal) return;

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
            // Initialiser à l'ouverture du modal
            toggleAutreField();
        }

        openModalButton.addEventListener('click', () => {
            createModal.classList.remove('hidden');
            createModal.classList.add('flex');
            toggleAutreField();
        });
        
        const close = () => {
            createModal.classList.remove('flex');
            createModal.classList.add('hidden');
            // Réinitialiser le formulaire
            form?.reset();
            toggleAutreField();
        }
        
        closeModalButton?.addEventListener('click', close);
        cancelModal?.addEventListener('click', close);
    })();
</script>
