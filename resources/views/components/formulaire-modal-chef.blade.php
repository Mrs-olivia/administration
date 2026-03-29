@php
    $servicesChefModal = config('administration.services', []);
@endphp
<div id="createChefModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Nouveau formulaire (chef)</h3>
            <button id="closeChefModalButton" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form method="POST" action="{{ route('chefService.forms.store') }}" enctype="multipart/form-data">
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
                @php
                    $chefService = auth()->user()->service_code;
                    $chefAnnee = (int) old('annee', date('Y'));
                    $chefPeek = $chefService ? \App\Models\Formulaire::peekNextNumeroOrdre($chefService, $chefAnnee) : null;
                @endphp
                <div class="rounded-lg border border-blue-200 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-950/30 p-3 text-sm">
                    <p class="font-medium text-gray-800 dark:text-gray-100">Référence (attribuée automatiquement)</p>
                    @if ($chefService)
                        <input type="hidden" name="service_code" value="{{ $chefService }}" />
                        <p class="mt-1 text-gray-700 dark:text-gray-300">
                            Service : <strong>{{ $servicesChefModal[$chefService] ?? $chefService }}</strong>
                            (code <code class="text-xs">{{ $chefService }}</code>)
                        </p>
                        <div class="mt-2 grid grid-cols-2 gap-3 items-end">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Année</label>
                                <input type="hidden" name="annee" value="{{ $chefAnnee }}" />
                                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $chefAnnee }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">N° d’ordre (indicatif)</p>
                                <p class="mt-1 font-mono text-sm font-semibold text-blue-900 dark:text-blue-200">{{ $chefService }}/{{ $chefAnnee }}-{{ str_pad((string) $chefPeek, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                    @else
                        <p class="mt-1 text-amber-800 dark:text-amber-200 text-xs">
                            Votre compte n’est pas rattaché à un service. Contactez l’administrateur — vous ne pourrez pas enregistrer de formulaire.
                        </p>
                    @endif
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
                        <input id="date_reception_chef_modal" name="date_reception" type="date" value="{{ old('date_reception') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Date échéance</label>
                        <input id="date_echeance_chef_modal" name="date_echeance" type="date" value="{{ old('date_echeance') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Type document</label>
                        <select id="type_document_chef_modal" name="type_document" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
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
                        <input type="text" id="autre_type_document_chef_modal" name="autre_type_document" placeholder="Précisez le type de document"
                            class="mt-2 hidden block w-full rounded-md border-gray-300 shadow-sm" value="{{ old('autre_type_document') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fichier</label>
                        <input name="fichier" type="file" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    Le formulaire sera transmis au secrétariat. Le secrétariat pourra uniquement l’archiver.
                </p>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button type="button" id="cancelChefModal" class="px-4 py-2 bg-gray-200 rounded">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Envoyer au secrétariat</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        const openModalButton = document.getElementById('openChefModalButton');
        const createModal = document.getElementById('createChefModal');
        const closeModalButton = document.getElementById('closeChefModalButton');
        const cancelModal = document.getElementById('cancelChefModal');
        const form = createModal?.querySelector('form');
        const typeDocumentSelect = document.getElementById('type_document_chef_modal');
        const autreTypeDocumentInput = document.getElementById('autre_type_document_chef_modal');
        const dateReceptionChef = document.getElementById('date_reception_chef_modal');
        const dateEcheanceChef = document.getElementById('date_echeance_chef_modal');
        const dateOrderMsgChef = 'Impossible d\'avoir une date d\'échéance inférieure à la date de réception.';

        const syncEcheanceMinChef = () => {
            const r = dateReceptionChef?.value;
            if (dateEcheanceChef) {
                dateEcheanceChef.min = r || '';
                if (r && dateEcheanceChef.value && dateEcheanceChef.value < r) {
                    dateEcheanceChef.value = r;
                }
            }
        };

        if (!openModalButton || !createModal) return;

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
            toggleAutreField();
        }

        const shouldShowOnLoad = @json($errors->any());
        if (shouldShowOnLoad) {
            createModal.classList.remove('hidden');
            createModal.classList.add('flex');
            toggleAutreField();
            syncEcheanceMinChef();
        }

        dateReceptionChef?.addEventListener('change', syncEcheanceMinChef);
        dateReceptionChef?.addEventListener('input', syncEcheanceMinChef);

        openModalButton.addEventListener('click', () => {
            createModal.classList.remove('hidden');
            createModal.classList.add('flex');
            toggleAutreField();
            syncEcheanceMinChef();
        });

        form?.addEventListener('submit', function(e) {
            const r = dateReceptionChef?.value;
            const ec = dateEcheanceChef?.value;
            if (r && ec && ec < r) {
                e.preventDefault();
                alert(dateOrderMsgChef);
            }
        });

        const close = () => {
            createModal.classList.remove('flex');
            createModal.classList.add('hidden');
            form?.reset();
            toggleAutreField();
        };

        closeModalButton?.addEventListener('click', close);
        cancelModal?.addEventListener('click', close);
    })();
</script>

