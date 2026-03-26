<x-app-layout>
    <div class="p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center mb-4">
            <h1 class="text-2xl font-bold">Gestion des utilisateurs</h1>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center justify-center bg-blue-950 hover:bg-blue-900 text-white px-4 py-2 rounded-lg transition text-sm font-medium">
                    Créer un compte (secrétaire / chef)
                </a>
                <button id="openUserModalButton" type="button"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition text-sm">
                    + Ajout rapide (modal)
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900/30 dark:border-green-700 dark:text-green-200">
                <p>{{ session('success') }}</p>
                @if(session('created_email'))
                    <p class="mt-2 text-sm font-medium">E-mail du compte créé : {{ session('created_email') }}</p>
                @endif
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg dark:bg-red-900/30 dark:border-red-700 dark:text-red-200">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- MODAL AJOUT/MODIFICATION --}}
        <div id="userModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-xl p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="modalTitle">Ajouter un utilisateur</h3>
                    <button id="closeUserModal" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
                </div>

                <form id="userForm" method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="user_id" id="userId">

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-400">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Nom</label>
                            <input name="name" id="name" type="text" required value="{{ old('name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Email</label>
                            <input name="email" id="email" type="email" required value="{{ old('email') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Mot de passe</label>
                                <input name="password" id="password" type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                <p class="text-xs text-gray-500 mt-1" id="passwordHelp">Règles du mot de passe Laravel (min. 8 caractères, etc.)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Confirmer</label>
                                <input name="password_confirmation" id="password_confirmation" type="password"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div id="roleBlockStaff">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Rôle</label>
                            <select name="role" id="roleSelect" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Choisir un rôle --</option>
                                <option value="secretaire">Secrétaire</option>
                                <option value="chef_de_service">Chef de service</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1" id="roleHelpCreate">Création : secrétaire ou chef uniquement. Communiquez e-mail et mot de passe à l’utilisateur.</p>
                            <p class="text-xs text-gray-500 mt-1 hidden" id="roleHelpEdit">Modification : secrétaire ou chef de service uniquement. L’administrateur est défini au déploiement (seeder).</p>
                        </div>
                        <div id="roleBlockAdmin" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Rôle</label>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 rounded-md border border-gray-200 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 px-3 py-2">
                                Administrateur (compte initial — non modifiable)
                            </p>
                            <input type="hidden" name="role" value="admin" id="roleHiddenAdmin" disabled />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" id="cancelUserModal" 
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                            Annuler
                        </button>
                        <button type="submit" id="submitBtn" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLEAU DES UTILISATEURS --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($user->role == 'admin') bg-purple-100 text-purple-800
                                    @elseif($user->role == 'secretaire') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    @if($user->role === 'chef_de_service')
                                        Chef de service
                                    @else
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')"
                                    class="text-yellow-600 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 px-3 py-1 rounded-lg transition">
                                    Modifier
                                </button>

                                @if($user->role !== 'admin')
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')"
                                            class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-3 py-1 rounded-lg transition">
                                            Supprimer
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                Aucun utilisateur trouvé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <script>
        // Configuration du modal
        const userModal = document.getElementById('userModal');
        const openModalBtn = document.getElementById('openUserModalButton');
        const closeModalBtn = document.getElementById('closeUserModal');
        const cancelModalBtn = document.getElementById('cancelUserModal');
        const userForm = document.getElementById('userForm');
        const modalTitle = document.getElementById('modalTitle');
        const formMethod = document.getElementById('formMethod');
        const userId = document.getElementById('userId');
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const roleSelect = document.getElementById('roleSelect');
        const roleBlockStaff = document.getElementById('roleBlockStaff');
        const roleBlockAdmin = document.getElementById('roleBlockAdmin');
        const roleHiddenAdmin = document.getElementById('roleHiddenAdmin');
        const passwordHelp = document.getElementById('passwordHelp');
        const submitBtn = document.getElementById('submitBtn');

        const roleHelpCreate = document.getElementById('roleHelpCreate');
        const roleHelpEdit = document.getElementById('roleHelpEdit');

        function setRoleUiMode(mode) {
            if (mode === 'admin') {
                roleBlockStaff.classList.add('hidden');
                roleBlockAdmin.classList.remove('hidden');
                roleSelect.removeAttribute('required');
                roleSelect.disabled = true;
                roleHiddenAdmin.disabled = false;
            } else {
                roleBlockStaff.classList.remove('hidden');
                roleBlockAdmin.classList.add('hidden');
                roleSelect.disabled = false;
                roleSelect.setAttribute('required', 'required');
                roleHiddenAdmin.disabled = true;
            }
        }

        openModalBtn.addEventListener('click', () => {
            resetForm();
            setRoleUiMode('staff');
            roleHelpCreate.classList.remove('hidden');
            roleHelpEdit.classList.add('hidden');
            modalTitle.textContent = 'Ajouter un utilisateur';
            formMethod.value = 'POST';
            userForm.action = "{{ route('admin.users.store') }}";
            passwordInput.required = true;
            passwordConfirmation.required = true;
            passwordHelp.textContent = 'Règles Laravel (min. 8 caractères, etc.) — requis';
            userModal.classList.remove('hidden');
            userModal.classList.add('flex');
        });

        // Fonction pour éditer un utilisateur
        window.editUser = function(id, name, email, role) {
            resetForm();
            if (role === 'admin') {
                setRoleUiMode('admin');
            } else {
                setRoleUiMode('staff');
                roleSelect.value = role;
            }
            roleHelpCreate.classList.add('hidden');
            roleHelpEdit.classList.remove('hidden');
            modalTitle.textContent = 'Modifier l\'utilisateur';
            formMethod.value = 'PUT';
            userId.value = id;
            userForm.action = "{{ url('/admin/users') }}/" + id;
            nameInput.value = name;
            emailInput.value = email;
            passwordInput.required = false;
            passwordConfirmation.required = false;
            passwordHelp.textContent = 'Laissez vide pour conserver le mot de passe actuel';

            userModal.classList.remove('hidden');
            userModal.classList.add('flex');
        };

        // Fermer le modal
        function closeModal() {
            userModal.classList.add('hidden');
            userModal.classList.remove('flex');
            resetForm();
        }

        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);

        // Fermer le modal en cliquant à l'extérieur
        userModal.addEventListener('click', (e) => {
            if (e.target === userModal) {
                closeModal();
            }
        });

        // Réinitialiser le formulaire
        function resetForm() {
            userForm.reset();
            userId.value = '';
            setRoleUiMode('staff');
            passwordInput.required = true;
            passwordConfirmation.required = true;
            passwordHelp.textContent = 'Minimum 8 caractères';
            
            // Réinitialiser les erreurs de validation
            const errorDivs = document.querySelectorAll('.text-red-600');
            errorDivs.forEach(div => div.remove());
        }

        // Validation du formulaire côté client
        userForm.addEventListener('submit', (e) => {
            const password = passwordInput.value;
            const confirmPassword = passwordConfirmation.value;
            
            // Supprimer les anciens messages d'erreur
            const oldErrors = document.querySelectorAll('.text-red-600');
            oldErrors.forEach(error => error.remove());

            // Vérifier si le mot de passe est requis (ajout) ou optionnel (modification)
            if (formMethod.value === 'POST' || password.length > 0 || confirmPassword.length > 0) {
                if (password.length < 8) {
                    e.preventDefault();
                    showError(passwordInput, 'Le mot de passe doit contenir au moins 8 caractères');
                } else if (password !== confirmPassword) {
                    e.preventDefault();
                    showError(passwordConfirmation, 'Les mots de passe ne correspondent pas');
                }
            }
        });

        function showError(input, message) {
            const errorDiv = document.createElement('p');
            errorDiv.className = 'text-red-600 text-xs mt-1';
            errorDiv.textContent = message;
            input.parentNode.appendChild(errorDiv);
            input.classList.add('border-red-500');
        }

        // Nettoyer les bordures rouges lors de la saisie
        [nameInput, emailInput, passwordInput, passwordConfirmation, roleSelect, roleHiddenAdmin].forEach(input => {
            if (input) {
                input.addEventListener('input', () => {
                    input.classList.remove('border-red-500');
                    const errorMsg = input.parentNode.querySelector('.text-red-600');
                    if (errorMsg) errorMsg.remove();
                });
            }
        });
    </script>
</x-app-layout>