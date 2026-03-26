<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Notifications</h2>
            @if(auth()->user()->unreadNotifications->isNotEmpty())
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-blue-700 hover:text-blue-900 dark:text-blue-400">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">{{ session('success') }}</div>
        @endif

        <ul class="divide-y divide-gray-200 dark:divide-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
            @forelse($notifications as $n)
                @php
                    $data = $n->data;
                @endphp
                <li class="{{ $n->read_at ? 'bg-white dark:bg-gray-800' : 'bg-blue-50/80 dark:bg-blue-950/30' }}">
                    <a href="{{ route('notifications.open', $n->id) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <p class="font-medium text-gray-900 dark:text-white">{{ $data['title'] ?? 'Notification' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $data['body'] ?? '' }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $n->created_at->diffForHumans() }}</p>
                    </a>
                </li>
            @empty
                <li class="p-8 text-center text-gray-500 dark:text-gray-400">Aucune notification.</li>
            @endforelse
        </ul>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</x-app-layout>
