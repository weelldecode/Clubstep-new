<?php

namespace App\Livewire\Settings;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public string $pin = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($this->pin, Auth::user()->pin)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'pin' => __('PIN inválido.'),
            ]);
        }

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}
