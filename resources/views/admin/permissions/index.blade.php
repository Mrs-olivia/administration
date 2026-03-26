<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Matrice des permissions
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Les permissions sont ajustables pour chaque rôle (y compris l’administrateur). Vous confirmerez chaque changement.
            </p>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900 dark:bg-blue-950/40 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-medium text-gray-900 dark:text-gray-100">Étape suivante : utilisateurs</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">Créez ou gérez les comptes depuis la page dédiée (modal d’ajout).</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex shrink-0 items-center justify-center rounded-lg bg-blue-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    Créer un compte (secrétaire / chef)
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex shrink-0 items-center justify-center rounded-lg border border-blue-800 bg-white px-5 py-2.5 text-sm font-semibold text-blue-950 shadow-sm hover:bg-blue-50 dark:border-blue-600 dark:bg-gray-800 dark:text-blue-200 dark:hover:bg-gray-700">
                    Liste des utilisateurs
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.permissions.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th scope="col" class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                Permission
                            </th>
                            @foreach($roles as $role)
                                <th scope="col" class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300 min-w-[7rem]">
                                    {{ $role->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($permissions as $permission)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/40">
                                <td class="sticky left-0 z-10 bg-white px-4 py-3 text-sm text-gray-900 dark:bg-gray-800 dark:text-gray-100">
                                    <span class="font-medium">{{ $labels[$permission->name] ?? $permission->name }}</span>
                                    <span class="mt-0.5 block text-xs text-gray-500 font-mono">{{ $permission->name }}</span>
                                </td>
                                @foreach($roles as $role)
                                    <td class="px-3 py-3 text-center align-middle">
                                        @php
                                            $roleColorClass = match ($role->name) {
                                                'admin' => 'text-blue-600 focus:ring-blue-500',
                                                'secretaire' => 'text-red-600 focus:ring-red-500',
                                                'chef_de_service' => 'text-yellow-500 focus:ring-yellow-500',
                                                default => 'text-blue-600 focus:ring-blue-500',
                                            };
                                        @endphp
                                        <input type="checkbox"
                                               name="matrix[{{ $role->id }}][]"
                                               value="{{ $permission->id }}"
                                               @checked($role->hasPermissionTo($permission))
                                               class="permission-toggle h-4 w-4 rounded border-gray-300 {{ $roleColorClass }} dark:border-gray-600 dark:bg-gray-700"
                                               data-prev-checked="{{ $role->hasPermissionTo($permission) ? 'true' : 'false' }}"
                                               data-permission="{{ $permission->name }}"
                                               data-permission-label="{{ $labels[$permission->name] ?? $permission->name }}"
                                               data-role="{{ $role->name }}"
                                        >
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Cochez/décochez les permissions pour chaque rôle, puis enregistrez. Une confirmation vous sera demandée à chaque changement.
                </p>
                <button type="submit"
                        class="inline-flex items-center rounded-lg bg-blue-950 px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    Enregistrer les permissions
                </button>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('input.permission-toggle').forEach((checkbox) => {
                    checkbox.addEventListener('change', () => {
                        if (checkbox.dataset.reverting === '1') return;

                        const prevChecked = checkbox.dataset.prevChecked === 'true';
                        const nowChecked = checkbox.checked;
                        if (prevChecked === nowChecked) return;

                        const permissionLabel = checkbox.dataset.permissionLabel || checkbox.dataset.permission || '';
                        const roleName = checkbox.dataset.role || '';
                        const verb = nowChecked ? 'cocher' : 'décocher';
                        const msg = `Succès : ${verb} la permission "${permissionLabel}" pour le rôle "${roleName}" ?`;

                        if (!window.confirm(msg)) {
                            checkbox.dataset.reverting = '1';
                            checkbox.checked = prevChecked;
                            checkbox.dataset.prevChecked = String(prevChecked);
                            checkbox.dataset.reverting = '0';
                            return;
                        }

                        checkbox.dataset.prevChecked = String(nowChecked);
                    });
                });
            });
        </script>
    </div>
</x-app-layout>
