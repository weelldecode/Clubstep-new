<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout("components.layouts.auth")]
class ConfirmPassword extends Component
{
    public string $pin = "";

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            "pin" => ["required", "digits:6"],
        ]);

        $user = Auth::user();
        if (!$user || !\Illuminate\Support\Facades\Hash::check($this->pin, $user->pin)) {
            throw ValidationException::withMessages([
                "pin" => __("PIN inválido."),
            ]);
        }

        session(["auth.password_confirmed_at" => time()]);

        $this->redirectIntended(
            default: route("home", absolute: false),
            navigate: true,
        );
    }
}
