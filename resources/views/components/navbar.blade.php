@php
    $user = auth()->user();
    $role = $user?->role;

    $homeDashboardUrl = $user
        ? match ($role) {
            'admin' => route('admin.permissions.index'),
            'secretaire' => route('secretaire.dashboard'),
            'chef_de_service' => route('chefService.dashboard'),
            default => route('dashboard'),
        }
        : route('dashboard');

    $dashboardLabel = match ($role) {
        'admin' => 'Tableau de bord',
        'secretaire' => 'Tableau de bord',
        'chef_de_service' => 'Tableau de bord',
        default => 'Dashboard',
    };
@endphp

<div id="drawer-navigation" x-cloak class="fixed top-0 left-0 z-40 w-64 h-screen p-4 overflow-y-auto transition-transform bg-neutral-primary-soft border-e border-default" :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'" tabindex="-1" aria-labelledby="drawer-navigation-label">
  <div class="border-b border-default pb-4 flex items-center">
      <a href="{{ $homeDashboardUrl }}" class="flex items-center space-x-2 rtl:space-x-reverse">
         <span class="self-center text-lg font-semibold whitespace-nowrap text-heading">UNIKIN</span>
      </a>
      <button type="button" @click="drawerOpen = false" aria-controls="drawer-navigation" class="text-body bg-transparent hover:text-heading hover:bg-neutral-tertiary rounded-base w-9 h-9 absolute top-2.5 end-2.5 flex items-center justify-center">
         <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
         <span class="sr-only">Fermer le menu</span>
      </button>
   </div>

  <div class="py-5 overflow-y-auto">
      <p class="px-2 mb-3 text-xs font-semibold uppercase tracking-wide text-fg-muted">Navigation</p>
      <ul class="space-y-1 font-medium">

         @if($role === 'admin')
            <li>
               <a href="{{ route('admin.permissions.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('admin.permissions.*') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="w-5 h-5 shrink-0 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                  <span class="ms-3">Permissions</span>
               </a>
            </li>
            <li>
               <a href="{{ route('admin.users.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('admin.users.index', 'admin.users.show') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M16 19h4a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-2m-2.236-4a3 3 0 1 0 0-4M3 18v-1a3 3 0 0 1 3-3h4a3 3 0 0 1 3 3v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-10a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
                  <span class="ms-3">Utilisateurs</span>
               </a>
            </li>
            <li>
               <a href="{{ route('admin.users.create') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('admin.users.create') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                  <span class="ms-3">Créer un compte</span>
               </a>
            </li>
            <li>
               <a href="{{ route('role.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('role.*') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  <span class="ms-3">Rôles</span>
               </a>
            </li>
         @elseif($role === 'secretaire')
            <li>
               <a href="{{ route('secretaire.dashboard') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('secretaire.dashboard') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="w-5 h-5 shrink-0 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z"/></svg>
                  <span class="ms-3">{{ $dashboardLabel }}</span>
               </a>
            </li>
            <li>
               <a href="{{ route('secretaire.forms.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('secretaire.forms.*') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  <span class="ms-3 font-medium">Formulaires</span>
               </a>
            </li>
            <li>
               <a href="{{ route('secretaire.forms.index', ['status' => \App\Models\Formulaire::STATUS_ARCHIVE]) }}"
                  class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ (int) request('status') === \App\Models\Formulaire::STATUS_ARCHIVE ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 6 9-6"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5h8"/></svg>
                  <span class="ms-3 font-medium">Archives</span>
               </a>
            </li>
            <li>
               <a href="{{ route('secretaire.workflow_logs.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('secretaire.workflow_logs.*') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2-10H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V8a2 2 0 00-2-2z"/></svg>
                  <span class="ms-3">Logs workflow</span>
               </a>
            </li>
         @elseif($role === 'chef_de_service')
            <li>
               <a href="{{ route('chefService.dashboard') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('chefService.dashboard') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="w-5 h-5 shrink-0 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z"/></svg>
                  <span class="ms-3">{{ $dashboardLabel }}</span>
               </a>
            </li>
            <li>
               <a href="{{ route('chefService.forms.index') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('chefService.forms.*') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  <span class="ms-3 font-medium">Formulaires à traiter</span>
               </a>
            </li>
            <li>
               <a href="{{ route('chefService.forms.index', ['status' => \App\Models\Formulaire::STATUS_ARCHIVE]) }}"
                  class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ (int) request('status') === \App\Models\Formulaire::STATUS_ARCHIVE ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l9 6 9-6"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5h8"/></svg>
                  <span class="ms-3 font-medium">Archives</span>
               </a>
            </li>
            <li>
               <a href="{{ route('chefService.forms.created') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('chefService.forms.created') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
                  <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  <span class="ms-3 font-medium">Formulaires créés</span>
               </a>
            </li>
         @else
            <li>
               <a href="{{ $homeDashboardUrl }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">
                  <svg class="w-5 h-5 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6.025A7.5 7.5 0 1 0 17.975 14H10V6.025Z"/><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 3c-.169 0-.334.014-.5.025V11h7.975c.011-.166.025-.331.025-.5A7.5 7.5 0 0 0 13.5 3Z"/></svg>
                  <span class="ms-3">{{ $dashboardLabel }}</span>
               </a>
            </li>
         @endif

         <li class="pt-4 mt-4 border-t border-default">
            <p class="px-2 mb-2 text-xs font-semibold uppercase tracking-wide text-fg-muted">Compte</p>
         </li>
         <li>
            <a href="{{ route('profile.edit') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group {{ request()->routeIs('profile.*') ? 'bg-neutral-tertiary text-fg-brand' : '' }}">
               <svg class="shrink-0 w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
               <span class="ms-3">Profil</span>
            </a>
         </li>
         <li>
            <form method="POST" action="{{ route('logout') }}" class="flex items-center px-2 py-1.5 text-body rounded-base hover:bg-neutral-tertiary hover:text-fg-brand group">
               @csrf
               <button type="submit" class="flex items-center w-full text-left">
                  <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/></svg>
                  <span class="ms-3 whitespace-nowrap">Déconnexion</span>
               </button>
            </form>
         </li>
      </ul>
   </div>
</div>
