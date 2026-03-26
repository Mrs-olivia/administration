<nav id="topNav" x-data="{ open: false }" class="relative z-50 bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @php
                        $navDashboardUrl = auth()->check()
                            ? match (auth()->user()->role) {
                                'admin' => route('admin.permissions.index'),
                                'secretaire' => route('secretaire.dashboard'),
                                'chef_de_service' => route('chefService.dashboard'),
                                default => route('dashboard'),
                            }
                            : route('dashboard');
                    @endphp
                    <a href="{{ $navDashboardUrl }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="$navDashboardUrl" :active="request()->routeIs('dashboard', 'secretaire.dashboard', 'chefService.dashboard', 'admin.permissions.index')">
                        Tableau de bord
                    </x-nav-link>
                </div>
            </div>

            <!-- Menu latéral + Notifications + Profil -->
            <div class="hidden sm:flex sm:items-center sm:gap-3 sm:ms-6">
                @auth
                    <a href="{{ route('notifications.index') }}"
                       class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-blue-700 dark:hover:text-blue-400">
                        Notifications
                        @php $uc = auth()->user()->unreadNotifications->count(); @endphp
                        @if($uc > 0)
                            <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-xs font-bold text-white bg-red-600 rounded-full">{{ $uc > 99 ? '99+' : $uc }}</span>
                        @endif
                    </a>
                @endauth
                <button
                    type="button"
                    @click="drawerOpen = !drawerOpen"
                    aria-controls="drawer-navigation"
                    aria-expanded="false"
                    :aria-expanded="drawerOpen"
                    class="text-black bg-brand box-border border border-transparent hover:bg-brand-strong shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand">
                    Menu
                </button>

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-200 dark:border-gray-600 text-sm font-medium rounded-md text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                            Profil
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @auth
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</p>
                            </div>
                        @endauth
                        <x-dropdown-link :href="route('profile.edit')">
                            Mon profil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Déconnexion
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile : menu latéral + hamburger -->
            <div class="-me-2 flex items-center gap-2 sm:hidden">
                <button
                    type="button"
                    @click="drawerOpen = !drawerOpen"
                    aria-controls="drawer-navigation"
                    class="text-black bg-brand box-border border border-transparent hover:bg-brand-strong shadow-xs font-medium rounded-base text-sm px-3 py-2 focus:outline-none">
                    Menu
                </button>
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="$navDashboardUrl" :active="request()->routeIs('dashboard', 'secretaire.dashboard', 'chefService.dashboard', 'admin.permissions.index')">
                Tableau de bord
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="px-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                    <p class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</p>
                </div>
            @endauth

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('notifications.index')">
                    Notifications
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.edit')">
                    Mon profil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Déconnexion
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
