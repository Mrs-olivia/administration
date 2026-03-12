<x-app-layout>
    <hr>
    <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Gestion des rôles</h2>
            <button id="openModalButton" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Ajouter un rôle</button>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">ID</th>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Nom du rôle</th>
                    <th class="py-3 px-6 text-right text-sm font-medium text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                    <tr class="border-t">
                        <td class="py-3 px-6">{{ $role->id }}</td>
                        <td class="py-3 px-6">{{ $role->name }}</td>
                        <td class="py-3 px-6 text-right">
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

       <x-ajout_role />
</x-app-layout>
