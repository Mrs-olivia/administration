<x-modal name="action-denied" maxWidth="md" :show="session()->has('modal_error')">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Action impossible</h3>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">{{ config('messages.action_denied') }}</p>
        <div class="mt-6 flex justify-end">
            <button type="button"
                @click="$dispatch('close-modal', 'action-denied')"
                class="inline-flex items-center rounded-lg bg-blue-950 px-4 py-2 text-sm font-medium text-white hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900">
                Fermer
            </button>
        </div>
    </div>
</x-modal>
