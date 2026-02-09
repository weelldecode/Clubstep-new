<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold">{{ t('Payments') }}</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-300">{{ t('Track and update payment statuses.') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <flux:input class="w-full" placeholder="{{ t('Search by user, email, plan, status, or gateway id...') }}" wire:model.live="search" />
        </div>
        @if($setupError)
            <div class="rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-700/40 dark:bg-amber-900/20 dark:text-amber-300">
                {{ $setupError }}
            </div>
        @endif

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-900/30">
                    <tr class="text-left">
                        <th class="p-3">Gateway ID</th>
                        <th class="p-3">{{ t('User') }}</th>
                        <th class="p-3">{{ t('Plan') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('amount')">{{ t('Amount') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('status')">{{ t('Status') }}</th>
                        <th class="cursor-pointer p-3" wire:click="toggleSort('paid_at')">{{ t('Paid at') }}</th>
                        <th class="w-40 p-3"></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($payments as $payment)
                        @php
                            $paymentUser = $payment->subscription?->user ?? $payment->order?->user;
                            $paymentLabel = $payment->subscription?->plan?->name
                                ?? ($payment->order ? t('One-time purchase') : '-');
                        @endphp
                        <tr class="border-t border-zinc-100 dark:border-zinc-900">
                            <td class="p-3 text-xs text-zinc-500 dark:text-zinc-400">{{ $payment->payment_id_mercadopago ?? '-' }}</td>
                            <td class="p-3">
                                <div class="font-medium">{{ $paymentUser?->name ?? '-' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $paymentUser?->email ?? '-' }}</div>
                            </td>
                            <td class="p-3">{{ $paymentLabel }}</td>
                            <td class="p-3">R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}</td>
                            <td class="p-3">{{ $payment->status }}</td>
                            <td class="p-3">{{ $payment->paid_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="outline" wire:click="openStatusModal({{ $payment->id }})">{{ t('Status') }}</flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="p-6 text-center text-zinc-500 dark:text-zinc-300" colspan="7">{{ t('No payments.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $payments->links() }}

        <flux:modal wire:model="showStatusModal" class="min-w-lg">
            <div class="space-y-4">
                <div>
                    <flux:heading size="lg">{{ t('Update Payment') }}</flux:heading>
                </div>

                <div class="grid gap-3">
                    <div>
                        <flux:label>{{ t('Status') }}</flux:label>
                        <x-select
                            wire:model.live="statusForm.status"
                            placeholder="{{ t('Select the status') }}"
                            :options="$statusOptions"
                            option-label="name"
                            option-value="id"
                        />
                        @error('statusForm.status') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <flux:label>{{ t('Paid at') }}</flux:label>
                        <flux:input type="date" wire:model.live="statusForm.paid_at" />
                        @error('statusForm.paid_at') <div class="mt-1 text-xs text-red-500">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="outline" wire:click="$set('showStatusModal', false)">{{ t('Cancel') }}</flux:button>
                    <flux:button wire:click="saveStatus">{{ t('Save') }}</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
