<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Créer un compte (secrétaire ou chef de service)
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-blue-700 hover:text-blue-900 dark:text-blue-400">
                ← Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl">
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950/40 dark:text-amber-100">
            <p class="font-medium">Après création</p>
            <p class="mt-1">Transmettez <strong>l’e-mail</strong> et le <strong>mot de passe</strong> à la personne (hors application). Elle utilisera uniquement la page <strong>Connexion</strong> — l’inscription publique est désactivée.</p>
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5">
                @csrf

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4 text-sm text-red-800 dark:bg-red-900/30 dark:text-red-200">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <x-input-label for="name" value="Nom complet" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                </div>

                <div>
                    <x-input-label for="email" value="Adresse e-mail (identifiant de connexion)" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required autocomplete="username" />
                </div>

                <div>
                    <x-input-label for="role" value="Rôle" />
                    <select id="role" name="role" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">— Choisir —</option>
                        <option value="secretaire" @selected(old('role') === 'secretaire')>Secrétaire</option>
                        <option value="chef_de_service" @selected(old('role') === 'chef_de_service')>Chef de service</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="service_code" value="Service" />
                    <select id="service_code" name="service_code" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="" disabled @selected(! old('service_code'))>— Choisir un service —</option>
                        @foreach (config('administration.services', []) as $code => $label)
                            <option value="{{ $code }}" @selected(old('service_code') === $code)>{{ $label }} ({{ $code }})</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Obligatoire pour secrétaire et chef (pas pour l’admin, créé au seeder).</p>
                </div>

                <div>
                    <x-input-label for="password" value="Mot de passe initial (à communiquer à l’utilisateur)" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">
                        Annuler
                    </a>
                    <x-primary-button>
                        Créer le compte
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
