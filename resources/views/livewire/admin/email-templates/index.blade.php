<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Email templates') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Edit HTML templates with dynamic variables.') }}</p>
            </div>
            <flux:button wire:click="openCreate">{{ t('New template') }}</flux:button>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by name, key, or subject...') }}" wire:model.live="search" />
        </div>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                    <tr class="text-left">
                        <th class="p-3">{{ t('Name') }}</th>
                        <th class="p-3">{{ t('Key') }}</th>
                        <th class="p-3">{{ t('Subject') }}</th>
                        <th class="p-3">{{ t('Active') }}</th>
                        <th class="p-3 w-40"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($templates as $template)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3 font-medium">{{ $template->name }}</td>
                            <td class="p-3 text-xs text-zinc-500 dark:text-zinc-400">{{ $template->key }}</td>
                            <td class="p-3">{{ $template->subject }}</td>
                            <td class="p-3">
                                <span class="inline-flex rounded-full px-2 py-1 text-[11px] font-semibold {{ $template->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                                    {{ $template->is_active ? t('Active') : t('Inactive') }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="toggleStatus({{ $template->id }})">
                                        {{ $template->is_active ? t('Deactivate') : t('Activate') }}
                                    </flux:button>
                                    <flux:button variant="outline" wire:click="openEdit({{ $template->id }})">{{ t('Edit') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="5">{{ t('No templates.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $templates->links() }}

        <flux:modal wire:model="showModal" class="max-w-4xl">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ $editingId ? t('Edit template') : t('New template') }}</flux:heading>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <flux:label>{{ t('Name') }}</flux:label>
                        <flux:input wire:model.live="form.name" />
                        @error('form.name') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <flux:label>{{ t('Key') }}</flux:label>
                        <flux:input wire:model.live="form.key" placeholder="ex: subscription_expiring" />
                        @error('form.key') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <flux:label>{{ t('Subject') }}</flux:label>
                    <flux:input wire:model.live="form.subject" />
                    @error('form.subject') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <flux:label>{{ t('Variables (comma-separated)') }}</flux:label>
                    <flux:input wire:model.live="form.variables" placeholder="user_name, renew_url" />
                </div>

                <div class="rounded-xl border border-zinc-200/70 bg-white/70 p-3 dark:border-zinc-800 dark:bg-zinc-900/70">
                    <div class="flex flex-wrap gap-2 text-xs">
                        @foreach ($suggestedVariables as $var)
                            <button
                                type="button"
                                class="rounded-full border border-zinc-200/60 bg-white px-3 py-1 text-zinc-600 transition hover:bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
                                x-on:click="window.dispatchEvent(new CustomEvent('insert-email-variable', { detail: '{{ $var }}' }))"
                            >
                                {{ $var }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <flux:label>{{ t('HTML') }}</flux:label>
                    <div
                        x-data="{
                            value: @entangle('form.body_html').defer,
                            init() {
                                this.$nextTick(() => {
                                    this.$refs.editor.innerHTML = this.value || '';
                                });
                                window.addEventListener('insert-email-variable', (e) => {
                                    this.insert('{{ ' + e.detail + ' }}');
                                });
                            },
                            sync() {
                                this.value = this.$refs.editor.innerHTML;
                            },
                            insert(text) {
                                document.execCommand('insertText', false, text);
                                this.sync();
                            },
                            cmd(command) {
                                document.execCommand(command, false, null);
                                this.sync();
                            }
                        }"
                        x-init="init()"
                        class="rounded-xl border border-zinc-200/70 bg-white/80 dark:border-zinc-800 dark:bg-zinc-900/70"
                    >
                        <div class="flex flex-wrap items-center gap-2 border-b border-zinc-200/70 px-3 py-2 text-xs text-zinc-500 dark:border-zinc-800 dark:text-zinc-300">
                            <button type="button" class="rounded px-2 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800" x-on:click="cmd('bold')">B</button>
                            <button type="button" class="rounded px-2 py-1 italic hover:bg-zinc-100 dark:hover:bg-zinc-800" x-on:click="cmd('italic')">I</button>
                            <button type="button" class="rounded px-2 py-1 underline hover:bg-zinc-100 dark:hover:bg-zinc-800" x-on:click="cmd('underline')">U</button>
                            <button type="button" class="rounded px-2 py-1 hover:bg-zinc-100 dark:hover:bg-zinc-800" x-on:click="cmd('insertUnorderedList')">• {{ t('List') }}</button>
                        </div>
                        <div
                            x-ref="editor"
                            contenteditable="true"
                            x-on:input="sync()"
                            class="min-h-[240px] px-4 py-3 text-sm text-zinc-700 outline-none dark:text-zinc-200"
                        ></div>
                    </div>
                    @error('form.body_html') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex items-center gap-2">
                    <flux:switch wire:model.live="form.is_active" />
                    <span class="text-sm text-zinc-600 dark:text-zinc-300">{{ t('Active template') }}</span>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="outline" wire:click="$set('showModal', false)">{{ t('Cancel') }}</flux:button>
                    <flux:button wire:click="save">{{ t('Save') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
