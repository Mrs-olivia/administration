<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Logs workflow (secrétariat)
        </h2>
    </x-slot>

    <section class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 p-4">
            <form method="GET" action="{{ route('secretaire.workflow_logs.index') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Expéditeur</label>
                    <input
                        type="text"
                        name="expediteur"
                        value="{{ $expediteur }}"
                        placeholder="Nom expéditeur"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    />
                </div>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-950 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    Rechercher
                </button>
            </form>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Quand</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Expéditeur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Détails</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($logs as $log)
                        @php
                            $exp = $log->formulaire?->expediteur ?? '—';
                            $eventLabel = match ($log->action) {
                                'secretaire_formulaire_created' => 'Formulaire créé par le secrétariat',
                                'secretaire_formulaire_updated' => 'Formulaire mis à jour par le secrétariat',
                                'secretaire_formulaire_deleted' => 'Formulaire supprimé par le secrétariat',
                                'secretaire_formulaire_sent_to_chef' => 'Dossier envoyé au chef par le secrétariat',
                                'secretaire_formulaire_archived' => 'Dossier archivé par le secrétariat',
                                'chef_formulaire_created' => 'Formulaire créé par le chef',
                                'chef_status_set_in_progress' => 'Le chef a pris en charge le dossier',
                                'chef_formulaire_annotated' => 'Le chef a ajouté une annotation',
                                'chef_formulaire_validated' => 'Le chef a validé le dossier',
                                'chef_formulaire_rejected' => 'Le chef a rejeté le dossier',
                                default => (string) $log->action,
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40">
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">
                                {{ $log->created_at?->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">
                                {{ $exp }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-100">
                                <div class="font-medium">{{ $eventLabel }}</div>
                                @if(!empty($log->details))
                                    <pre class="mt-2 whitespace-pre-wrap break-words text-xs bg-gray-100 dark:bg-gray-900 rounded-lg p-2">
{{ json_encode($log->details, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}
                                    </pre>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                Aucun log pour ce filtre.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </section>
</x-app-layout>

