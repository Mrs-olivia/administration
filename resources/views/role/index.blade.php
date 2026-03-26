<x-app-layout>
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Gestion des rôles</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 bg-blue-950 text-white rounded hover:bg-blue-900 transition text-sm">Matrice des permissions</a>
                <button id="openModalButton" type="button" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Ajouter un rôle</button>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded border border-red-300">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200 dark:bg-gray-700">
                <tr>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700 dark:text-gray-200">ID</th>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Nom du rôle</th>
                    <th class="py-3 px-6 text-right text-sm font-medium text-gray-700 dark:text-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr class="border-t border-gray-200 dark:border-gray-600">
                        <td class="py-3 px-6 text-gray-800 dark:text-gray-200">{{ $role->id }}</td>
                        <td class="py-3 px-6 text-gray-800 dark:text-gray-200">{{ $role->name }}</td>
                        <td class="py-3 px-6 text-right">
                            @if(in_array($role->name, ['admin', 'secretaire', 'chef_de_service'], true))
                                <span class="text-xs text-gray-500 dark:text-gray-400">Rôle système</span>
                            @else
                            <form action="{{ route('role.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Supprimer</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <x-ajout_role />
    </div>
</x-app-layout>
