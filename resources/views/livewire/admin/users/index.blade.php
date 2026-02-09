<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Users') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Manage platform users.') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by name or email...') }}" wire:model.live="search" />
        </div>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                    <tr class="text-left">
                        <th class="cursor-pointer p-3" wire:click="toggleSort('name')">{{ t('Name') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('email')">{{ t('Email') }}</th>
                        @if ($hasRole)
                            <th class="cursor-pointer p-3" wire:click="toggleSort('role')">{{ t('Role') }}</th>
                        @endif
                        @if ($hasType)
                            <th class="cursor-pointer p-3" wire:click="toggleSort('type')">{{ t('Type') }}</th>
                        @endif
                        @if ($hasLocale)
                            <th class="cursor-pointer p-3" wire:click="toggleSort('locale')">{{ t('Locale') }}</th>
                        @endif
                        @if ($hasEmailVerified)
                            <th class="p-3">{{ t('Verified') }}</th>
                        @endif
                        <th class="cursor-pointer p-3" wire:click="toggleSort('created_at')">{{ t('Created at') }}</th>
                        <th class="w-40 p-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3 font-medium">{{ $user->name }}</td>
                            <td class="p-3 text-zinc-500 dark:text-zinc-400">{{ $user->email }}</td>
                            @if ($hasRole)
                                <td class="p-3">{{ $user->role ?? '-' }}</td>
                            @endif
                            @if ($hasType)
                                <td class="p-3">{{ $user->type ?? '-' }}</td>
                            @endif
                            @if ($hasLocale)
                                <td class="p-3">{{ $user->locale ?? '-' }}</td>
                            @endif
                            @if ($hasEmailVerified)
                                <td class="p-3">
                                    <span class="inline-flex rounded-full px-2 py-1 text-[11px] font-semibold {{ $user->email_verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">
                                        {{ $user->email_verified_at ? t('Yes') : t('No') }}
                                    </span>
                                </td>
                            @endif
                            <td class="p-3">{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="openEdit({{ $user->id }})">{{ t('Edit') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="8">{{ t('No users.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}

        <flux:modal wire:model="showModal" class="min-w-xl">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ t('Edit user') }}</flux:heading>
                </div>

                <div class="grid gap-3">
                    <div>
                        <flux:label>{{ t('Name') }}</flux:label>
                        <flux:input wire:model.live="form.name" />
                        @error('form.name') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <flux:label>{{ t('Email') }}</flux:label>
                        <flux:input wire:model.live="form.email" type="email" />
                        @error('form.email') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>

                    @if ($hasRole)
                        <div>
                            <flux:label>{{ t('Role') }}</flux:label>
                            <x-select
                                wire:model.live="form.role"
                                placeholder="{{ t('Select the role') }}"
                                :options="$roleOptions"
                                option-label="name"
                                option-value="id"
                            />
                            @error('form.role') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if ($hasType)
                        <div>
                            <flux:label>{{ t('Type') }}</flux:label>
                            <x-select
                                wire:model.live="form.type"
                                placeholder="{{ t('Select the type') }}"
                                :options="$typeOptions"
                                option-label="name"
                                option-value="id"
                            />
                            @error('form.type') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if ($hasLocale)
                        <div>
                            <flux:label>{{ t('Locale') }}</flux:label>
                            <x-select
                                wire:model.live="form.locale"
                                placeholder="{{ t('Select the locale') }}"
                                :options="$localeOptions"
                                option-label="name"
                                option-value="id"
                            />
                            @error('form.locale') <div class="text-xs text-red-500 mt-1">{{ $message }}</div> @enderror
                        </div>
                    @endif

                    @if ($hasEmailVerified)
                        <div class="rounded-lg border border-zinc-200/70 bg-zinc-50/70 p-3 dark:border-zinc-800 dark:bg-zinc-900/50">
                            <flux:switch
                                wire:model.live="form.email_verified"
                                label="{{ t('Email verified') }}"
                                description="{{ t('Mark if the user email is verified.') }}"
                            />
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="outline" wire:click="$set('showModal', false)">{{ t('Cancel') }}</flux:button>
                    <flux:button wire:click="save">{{ t('Save') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
