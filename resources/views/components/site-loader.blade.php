<style>
    html[data-loader-done='1'] #site-global-loader {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
</style>

<div
    id="site-global-loader"
    class="fixed inset-0 z-[99999] pointer-events-none flex items-center justify-center bg-white text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 opacity-100 transition-opacity duration-500 ease-out"
    aria-live="polite"
    aria-busy="true"
>
    <div data-loader-panel class="relative flex flex-col items-center gap-4 transition-all duration-500 ease-out">
        <div class="absolute -inset-10 rounded-full bg-accent/20 blur-3xl"></div>

        <div class="relative flex items-center gap-1">
            <span class="text-4xl font-black tracking-tight text-zinc-900 dark:text-zinc-100">Club</span>
            <span class="text-4xl font-black tracking-tight text-accent">Step</span>
        </div>

        <div class="relative flex items-center gap-2">
            <span class="h-2 w-2 animate-pulse rounded-full bg-accent"></span>
            <span class="h-2 w-2 animate-pulse rounded-full bg-zinc-300 dark:bg-zinc-200 [animation-delay:120ms]"></span>
            <span class="h-2 w-2 animate-pulse rounded-full bg-zinc-400 dark:bg-zinc-400 [animation-delay:240ms]"></span>
        </div>

        <p class="text-xs tracking-[0.24em] text-zinc-500 dark:text-zinc-400 uppercase">{{ t('Loading') }}</p>
    </div>
</div>

<script>
    (function () {
        const loader = document.getElementById('site-global-loader');
        if (!loader) return;
        const panel = loader.querySelector('[data-loader-panel]');

        const instantHide = () => {
            loader.classList.add('opacity-0', 'pointer-events-none');
            loader.setAttribute('aria-hidden', 'true');
            loader.setAttribute('aria-busy', 'false');
            loader.style.display = 'none';
            document.documentElement.setAttribute('data-loader-done', '1');
        };

        const hide = () => {
            loader.classList.add('opacity-0', 'pointer-events-none');
            if (panel) {
                panel.classList.add('opacity-0', 'scale-95', 'translate-y-3', 'blur-sm');
            }
            loader.setAttribute('aria-hidden', 'true');
            loader.setAttribute('aria-busy', 'false');
            setTimeout(() => {
                loader.style.display = 'none';
                document.documentElement.setAttribute('data-loader-done', '1');
            }, 560);
        };

        // Em navegação Livewire (wire:navigate), não mostrar loader novamente.
        if (window.__clubstepLoaderDone) {
            instantHide();
            return;
        }

        window.__clubstepLoaderDone = true;
        document.documentElement.setAttribute('data-loader-done', '0');

        if (document.readyState === 'complete') {
            setTimeout(hide, 280);
        } else {
            window.addEventListener('load', () => setTimeout(hide, 280), { once: true });
        }
    })();
</script>
