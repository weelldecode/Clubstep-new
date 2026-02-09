<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout("components.layouts.auth")]
class Login extends Component
{
    #[Validate("required|string|email")]
    public string $email = "";

    #[Validate("required|digits:6")]
    public string $pin = "";

    public bool $remember = false;

    public bool $stepEmail = true; // controlar etapa atual
    public ?User $user = null;

    /**
     * Handle an incoming authentication request.
     */
    public function loginEmail(): void
    {
        $this->validateOnly("email");

        $this->user = User::where("email", $this->email)->first();

        if (!$this->user) {
            throw ValidationException::withMessages([
                "email" => __("auth.failed"),
            ]);
        }

        if (!$this->user->pin) {
            throw ValidationException::withMessages([
                "email" => __("Usuário não possui PIN cadastrado."),
            ]);
        }

        $this->stepEmail = false;
    }

    public function loginPin(): void
    {
        $this->validateOnly("pin");

        $this->ensureIsNotRateLimited();

        if (!$this->user || !Hash::check($this->pin, $this->user->pin)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                "pin" => __("PIN inválido."),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        Auth::login($this->user, $this->remember);

        Session::regenerate();

        if (!$this->user->hasVerifiedEmail()) {
            $this->redirect(route("verification.notice"));
        } else {
            $this->dispatch(
                "notify",
                message: "Você foi logado com sucesso!",
                type: "success",
            );
            $this->redirectIntended(
                default: route("home", absolute: true),
                navigate: true,
            );
        }
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            "pin" => __("auth.throttle", [
                "seconds" => $seconds,
                "minutes" => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        // usa email e IP para rate limiting
        return Str::lower($this->email) . "|" . request()->ip();
    }
}
