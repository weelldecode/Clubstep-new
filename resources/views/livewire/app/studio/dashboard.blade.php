<div  >
    <div class="relative h-[max(550px,calc(100dvh-90px))] max-h-350   rounded-4xl bg-grid px-4 sm:mx-4 sm:px-6 "> 
    <flux:header class="  flex flex-col items-start  w-full  h-32">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <div class=" w-full  ">
            <div class="h-16  px-10 flex items-center justify-between w-full  gap-10">

                <a href="{{ route('home') }}" class="text-accent flex items-center  rtl:space-x-reverse   " wire:navigate>
                    <flux:icon name="chevron-left" />
                </a>

                <div class="text-2xl font-bold text-zinc-700 dark:text-white transition-all duration-200">
                    {{ $name ?? t('Collection title') }}
                </div>
                <div>
                    <flux:button variant="primary">{{ t('Continue') }}</flux:button>
                </div>
            </div>
        </div>
    </flux:header>

    <section class="w-7xl mx-auto">

        <form action="" class="flex flex-col gap-5">
            <flux:field>
                <input wire:model.live="name" placeholder="{{ t('Collection title') }}"
                    class="text-3xl font-bold text-zinc-700 dark:text-white  placeholder-gray-400  dark:placeholder-gray-100 bg-transparent border-transparent focus:bg-transparent focus:border-transparent focus:ring-transparent"
                    type="text" />
                <flux:error name="name" />
            </flux:field>
            <flux:field>
                <textarea wire:model="description" placeholder="{{ t('Collection description') }}"
                    class="text-base font-medium text-zinc-700 dark:text-white  placeholder-gray-400  dark:placeholder-zinc-100 bg-transparent border-zinc-100 dark:border-transparent rounded-lg focus:bg-transparent focus:border-transparent focus:ring-transparent"></textarea>
                <flux:error name="description" />
            </flux:field>
            <flux:separator />
            <div> 
            {{-- Tags --}}
            <h4 class="font-semibold text-base text-zinc-600 dark:text-zinc-300">{{ t('Tags') }}:</h4>
            <ul class="flex flex-wrap gap-2  mt-2 ">
                @foreach ($availableTags as $tag)
                    <li>
                        <button type="button" wire:click="toggleTag('{{ $tag }}')"
                            class="px-3 py-1 rounded-full text-sm font-semibold border
                               @if (in_array($tag, $tags)) bg-accent text-white border-accent  cursor-pointer
                               @else bg-zinc-100 text-zinc-700 cursor-pointer border-zinc-300 dark:bg-zinc-800 dark:text-white dark:border-zinc-600 @endif
                               hover:opacity-80 transition">
                            {{ $tag }}
                        </button>
                    </li>
                @endforeach
            </ul>


            {{-- Categorias --}}
            <h4 class="mt-4 font-semibold text-base text-zinc-600 dark:text-zinc-300">{{ t('Categories') }}:</h4>
            <ul class="flex flex-wrap gap-2  mt-2">
                @foreach ($availableCategories as $c)
                    <li>
                        <button type="button" wire:click="setCategory({{ $c->id }})"
                            class="px-3 py-1 rounded-full text-sm font-semibold border transition
                        {{ $category === $c->id ? 'bg-blue-500 text-white border-blue-500  cursor-pointer' : ' cursor-pointer bg-zinc-100 text-zinc-700 border-zinc-300 dark:bg-zinc-800 dark:text-white dark:border-zinc-600' }}">
                            {{ $c->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
</div>
            <div class="mt-5">

                {{-- Preview dos arquivos --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @foreach ($files as $index => $file)
                        <div class="relative border border-zinc-100 dark:border-zinc-700  bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg shadow"
                            wire:key="file-{{ $file->getFilename() }}">
                            <p class="truncate">{{ $file->getClientOriginalName() }}</p>
                            <button type="button" wire:click="removeFile({{ $index }})"
                                class="absolute top-2 right-2 bg-red-600 text-white rounded-full cursor-pointer">
                                <flux:icon name="circle-x" />
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- Dropzone --}}
                @if (count($files) < $maxItems)
                    <div class="mt-5">
                        <div id="dropzone"
                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed dark:border-zinc-500 rounded-lg cursor-pointer bg-zinc-50 dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors relative"
                            :class="{ 'border-red-600 bg-red-50 dark:bg-red-900': hasError }">

                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center">
                                <flux:icon name="import" class="mx-auto size-8" />
                                <p class="mb-2 text-sm text-zinc-500 dark:text-zinc-50">
                                    <span class="font-semibold">{{ t('Click or drag') }}</span> {{ t('ZIP files here') }}
                                </p>
                                <p class="text-xs text-center text-zinc-500 dark:text-zinc-100">{{ t('Only ZIP files, up to 30MB') }}</p>

                            </div>

                            <input id="file-upload" type="file"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" wire:model="files"
                                multiple accept=".zip" />
                        </div>

                        {{-- Mensagem de erro --}}
                        <div id="file-error" class="mt-2 text-sm text-red-600 hidden"></div>
                    </div>
                @endif
            </div>
            <div class="mt-5">
                <p class="text-sm font-semibold tracking-wider">⚠️ {{ t('After submitting your item, it will go through a validation process. Our team will review the information, files, and selected categories before making the content available. This procedure ensures quality and security on the platform.') }}</p>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const dropzone = document.getElementById('dropzone');
                    const fileInput = document.getElementById('file-upload');
                    const errorDiv = document.getElementById('file-error');
                    const notZipMessage = @json(t('is not a ZIP file'));
                    const tooLargeMessage = @json(t('exceeds 30MB'));

                    let hasError = false;

                    function validateFiles(files) {
                        const validFiles = [];
                        const maxSize = 30 * 1024 * 1024; // 30MB
                        let errorMessages = [];

                        Array.from(files).forEach(file => {
                            const ext = file.name.split('.').pop().toLowerCase();
                            if (ext !== 'zip') {
                                errorMessages.push(`${file.name} ${notZipMessage}`);
                            } else if (file.size > maxSize) {
                                errorMessages.push(`${file.name} ${tooLargeMessage}`);
                            } else {
                                validFiles.push(file);
                            }
                        });

                        if (errorMessages.length > 0) {
                            errorDiv.innerHTML = errorMessages.join('<br>');
                            errorDiv.classList.remove('hidden');
                            dropzone.classList.add('border-red-600', 'bg-red-50', 'dark:bg-red-900');
                            hasError = true;
                        } else {
                            errorDiv.classList.add('hidden');
                            errorDiv.innerHTML = '';
                            dropzone.classList.remove('border-red-600', 'bg-red-50', 'dark:bg-red-900');
                            hasError = false;
                        }

                        const dataTransfer = new DataTransfer();
                        validFiles.forEach(f => dataTransfer.items.add(f));
                        fileInput.files = dataTransfer.files;
                    }

                    // Validar ao selecionar arquivos
                    fileInput.addEventListener('change', () => validateFiles(fileInput.files));

                    // Drag & Drop
                    dropzone.addEventListener('dragover', e => {
                        e.preventDefault();
                        dropzone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900');
                    });

                    dropzone.addEventListener('dragleave', e => {
                        e.preventDefault();
                        dropzone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900');
                    });

                    dropzone.addEventListener('drop', e => {
                        e.preventDefault();
                        dropzone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900');

                        const files = e.dataTransfer.files;
                        validateFiles(files);
                    });
                });
            </script>

        </form>
    </section></div>
</div>
