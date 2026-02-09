<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Password extends Component
{
    public string $current_pin = '';

    public string $pin = '';

    public string $pin_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePin(): void
    {
        try {
            $validated = $this->validate([
                'current_pin' => ['required', 'digits:6'],
                'pin' => ['required', 'digits:6', 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_pin', 'pin', 'pin_confirmation');

            throw $e;
        }

        if (!Hash::check($validated['current_pin'], Auth::user()->pin)) {
            $this->reset('current_pin', 'pin', 'pin_confirmation');
            throw ValidationException::withMessages([
                'current_pin' => __('PIN inválido.'),
            ]);
        }

        Auth::user()->update([
            'pin' => Hash::make($validated['pin']),
        ]);

        $this->reset('current_pin', 'pin', 'pin_confirmation');

        $this->dispatch('pin-updated');
    }
    public function render()
    {
        return view("livewire.settings.password")
            ->title("Pagina Inicial")
            ->layout("layouts.app");
    }
}
