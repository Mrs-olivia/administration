@props([
    'assignedService' => null,
])

@php
    $services = config('administration.services', []);
@endphp

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
                <div class="rounded-lg border border-blue-200 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-950/30 p-3 text-sm">
                    <p class="font-medium text-gray-800 dark:text-gray-100">Référence (attribuée automatiquement)</p>
                    @if ($assignedService)
                        <input type="hidden" name="service_code" id="service_code_modal" value="{{ $assignedService }}" />
                        <p class="mt-1 text-gray-700 dark:text-gray-300">
                            Service : <strong>{{ $services[$assignedService] ?? $assignedService }}</strong>
                            (code <code class="text-xs">{{ $assignedService }}</code>)
                        </p>
                    @else
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mt-2">Service</label>
                        <select name="service_code" id="service_code_modal" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">— Choisir —</option>
                            @foreach ($services as $code => $label)
                                <option value="{{ $code }}" @selected(old('service_code') === $code)>{{ $label }} ({{ $code }})</option>
                            @endforeach
                        </select>
                    @endif
                    <div class="mt-2 grid grid-cols-2 gap-3 items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Année</label>
                            <input type="number" name="annee" id="annee_modal" min="2000" max="2100"
                                value="{{ old('annee', (int) date('Y')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">N° d’ordre</p>
                            <p id="ref_preview_modal" class="mt-1 font-mono text-sm font-semibold text-blue-900 dark:text-blue-200">—</p>
                        </div>
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
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Le statut sera automatiquement « En attente ». Vous pourrez envoyer le dossier au chef depuis la liste.</p>

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
        const serviceSelect = document.getElementById('service_code_modal');
        const anneeInput = document.getElementById('annee_modal');
        const refPreview = document.getElementById('ref_preview_modal');
        const nextRefUrl = @json(route('secretaire.forms.nextReference'));

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

        async function refreshReferencePreview() {
            if (!refPreview) return;
            const sc = serviceSelect?.tagName === 'SELECT' ? serviceSelect.value : serviceSelect?.value;
            const annee = anneeInput?.value;
            if (!sc || !annee) {
                refPreview.textContent = '—';
                return;
            }
            try {
                const u = new URL(nextRefUrl, window.location.origin);
                u.searchParams.set('service_code', sc);
                u.searchParams.set('annee', annee);
                const res = await fetch(u.toString(), { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error();
                const data = await res.json();
                refPreview.textContent = data.reference_preview;
            } catch {
                refPreview.textContent = '—';
            }
        }

        const shouldShowOnLoad = @json($errors->any());
        if (shouldShowOnLoad) {
            createModal.classList.remove('hidden');
            createModal.classList.add('flex');
            toggleAutreField();
            refreshReferencePreview();
        }

        if (typeDocumentSelect) {
            typeDocumentSelect.addEventListener('change', toggleAutreField);
            toggleAutreField();
        }

        serviceSelect?.addEventListener('change', refreshReferencePreview);
        anneeInput?.addEventListener('change', refreshReferencePreview);
        anneeInput?.addEventListener('input', refreshReferencePreview);

        openModalButton.addEventListener('click', () => {
            createModal.classList.remove('hidden');
            createModal.classList.add('flex');
            toggleAutreField();
            refreshReferencePreview();
        });

        const close = () => {
            createModal.classList.remove('flex');
            createModal.classList.add('hidden');
            form?.reset();
            toggleAutreField();
            if (serviceSelect?.tagName === 'SELECT') {
                serviceSelect.value = '';
            }
            if (anneeInput) {
                anneeInput.value = @json((string) (int) date('Y'));
            }
            if (refPreview) refPreview.textContent = '—';
        };

        closeModalButton?.addEventListener('click', close);
        cancelModal?.addEventListener('click', close);
    })();
</script>
