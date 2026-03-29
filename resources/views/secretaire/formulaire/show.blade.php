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
            <div class="p-4 text-sm rounded-lg bg-green-100 text-green-800 border border-green-300" role="status">{{ session('success') }}</div>
        </div>
    @endif
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="p-4 text-sm rounded-lg bg-red-100 text-red-800 border border-red-300">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
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
                    <p class="text-blue-100 text-sm mt-1">
                        <span class="text-blue-200/90">Service initiateur :</span>
                        <span class="font-mono font-semibold">{{ $formulaire->initiatingServiceCode() }}</span>
                        <span class="text-blue-200/80">— {{ $formulaire->initiatingServiceLabel() }}</span>
                    </p>
                    @if($formulaire->isRelayServiceHold())
                        <p class="text-amber-100 text-xs mt-2 max-w-2xl leading-relaxed border border-amber-500/30 rounded-md px-2 py-1.5 bg-amber-950/20">
                            <strong>Relais :</strong> vous ne pouvez pas modifier ni archiver ce dossier ici. Après décision du chef, vous pouvez le <strong>transférer vers un autre service</strong> ou le <strong>renvoyer au service initiateur</strong> ; chaque transfert est consigné dans les logs workflow. L’archivage final est réservé au service initiateur, après une <strong>dernière validation ou rejet du chef sur place</strong>.
                        </p>
                    @endif
                    @if(filled($formulaire->origine_reference))
                        <p class="text-blue-200 text-sm mt-2 border-t border-blue-700/50 pt-2">
                            <span class="text-blue-100/90">Dossier d’origine :</span>
                            <span class="font-mono font-semibold">{{ $formulaire->origine_reference }}</span>
                            @if(filled($formulaire->origine_service_code))
                                <span class="text-blue-100/80">({{ $formulaire->origineServiceLabel() }})</span>
                            @endif
                        </p>
                    @endif
                    @if($formulaire->transfer_requires_secretary_edit)
                        <p class="text-amber-100 text-sm mt-2 max-w-2xl leading-relaxed border border-amber-500/40 rounded-md px-2 py-1.5 bg-amber-950/25">
                            <strong>Transfert inter-services :</strong> après le rejet du chef sur ce dossier revenu d’un circuit inter-services, vous devez <strong>modifier le dossier</strong> (au moins une enregistrement depuis la fiche « Éditer ») avant de pouvoir le transférer à nouveau.
                        </p>
                    @endif
                    @if($formulaire->hasTransferTrace())
                        <p class="text-teal-100 text-sm mt-2 border-t border-blue-700/50 pt-2">
                            <span class="text-teal-200/90">Transferts inter-services :</span>
                            <span class="font-semibold">{{ $formulaire->transfers_count }}</span>
                            @if($formulaire->first_transferred_at)
                                <span class="text-teal-200/80"> — premier : {{ $formulaire->first_transferred_at->format('d/m/Y H:i') }}</span>
                            @endif
                            @if($formulaire->last_transferred_at)
                                <span class="text-teal-200/80"> — dernier : {{ $formulaire->last_transferred_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </p>
                    @endif
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
                            <span class="text-sm text-gray-500 dark:text-gray-400">Service</span>
                            <p class="text-lg text-gray-900 dark:text-white">{{ $formulaire->serviceLabel() }}</p>
                            <p class="text-xs font-mono text-gray-500 dark:text-gray-400 mt-0.5">{{ $formulaire->service_code }}</p>
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

                    @if(filled(trim((string) $formulaire->note)))
                        <div class="rounded-lg border border-gray-200 dark:border-gray-600 p-4 bg-gray-50/80 dark:bg-gray-900/40">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Notes / historique</span>
                            <p class="mt-2 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $formulaire->note }}</p>
                        </div>
                    @endif

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
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Annotations et décisions chefs <span class="font-normal text-gray-500 dark:text-gray-400">([code service | date] — type — texte)</span></span>
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
                        <form action="{{ route('secretaire.forms.send', $formulaire) }}" method="POST" class="inline"
                              onsubmit="return confirm({{ $formulaire->sent_to_chef_at ? "'Voulez-vous renvoyer une notification au chef ?'" : "'Notifier le chef pour ce dossier ?'" }});">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm">
                                {{ $formulaire->sent_to_chef_at ? 'Renvoyer au chef' : 'Envoyer au chef' }}
                            </button>
                        </form>
                    @endcan

                    @can('delete', $formulaire)
                        <form action="{{ route('secretaire.forms.destroy', $formulaire) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement ce dossier ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">Supprimer</button>
                        </form>
                    @endcan

                    @can('transfer', $formulaire)
                        @php
                            $allowedTransferCodesShow = [];
                            foreach (array_keys(config('administration.services', [])) as $_c) {
                                if ($formulaire->isAllowedTransferTarget($_c)) {
                                    $allowedTransferCodesShow[] = $_c;
                                }
                            }
                        @endphp
                        @if(count($allowedTransferCodesShow))
                            <form action="{{ route('secretaire.forms.transfer', $formulaire) }}" method="POST" class="inline-flex flex-wrap items-center gap-2 rounded-lg border border-teal-200 dark:border-teal-900 bg-teal-50/50 dark:bg-teal-950/20 px-3 py-2"
                                  onsubmit="return confirm('Transférer ce dossier vers le service choisi ? Une nouvelle référence sera attribuée ; l’étape est enregistrée dans les logs.');">
                                @csrf
                                <span class="text-sm text-teal-900 dark:text-teal-100 self-center">Transférer vers</span>
                                <select name="target_service_code" required class="text-sm rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600">
                                    @foreach ($allowedTransferCodesShow as $code)
                                        <option value="{{ $code }}">{{ config('administration.services')[$code] ?? $code }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-teal-700 hover:bg-teal-800 text-white rounded-lg text-sm">Valider</button>
                            </form>
                        @endif
                    @endcan

                    @can('archive', $formulaire)
                        <form action="{{ route('secretaire.forms.archive', $formulaire) }}" method="POST" class="inline" onsubmit="return confirm('Archiver ce dossier ?');">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-700 hover:bg-purple-800 text-white rounded-lg text-sm">Archiver</button>
                        </form>
                    @elseif($formulaire->status !== \App\Models\Formulaire::STATUS_ARCHIVE)
                        <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm bg-gray-200 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500" title="{{ $formulaire->archiveDisabledHintForSecretaire() }}">Archiver</span>
                    @endcan
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
