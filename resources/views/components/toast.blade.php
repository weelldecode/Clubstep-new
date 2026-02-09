<div
    x-data="{ toasts: [] }"
    x-on:notify.window="
        toasts.push({
            id: Date.now(),
            message: $event.detail.message,
            type: $event.detail.type ?? 'info'
        });
        setTimeout(() => toasts.shift(), 3500);
    "
    class="fixed bottom-5 left-5 right-5  space-y-3 max-w-lg mx-auto  transition-all duration-500 ease-in-out"
    style="z-index: 9999999;">
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 scale-95"
            x-transition:enter-end="translate-y-0 opacity-100 scale-100"
            x-transition:leave="transform ease-in duration-200 transition"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            :class="{
                'bg-green-600 text-white': toast.type === 'success',
                'bg-red-600 text-white': toast.type === 'error',
                'bg-blue-600 text-white': toast.type === 'info',
            }"
            class="px-4 py-3 rounded-lg shadow-lg flex justify-center items-center space-x-2 transition-all duration-500 ease-in-out"
        >
            <!-- Ícones -->
            <svg x-show="toast.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <svg x-show="toast.type === 'error'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <svg x-show="toast.type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
            </svg>

            <!-- Mensagem -->
            <span x-text="toast.message" class="font-medium"></span>
        </div>
    </template>
</div>
