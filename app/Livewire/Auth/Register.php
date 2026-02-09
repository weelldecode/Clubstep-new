<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $pin = '';
    public string $pin_confirmation = '';

    public int $step = 1;

    /**
     * Handle an incoming registration request.
     */

    public function register()
    {
        $this->validateStep(); // valida passo 2  

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'pin' => Hash::make($this->pin),
        ]);

        event(new Registered($user));  // Isso envia o email de verificação
        Auth::login($user);

        $this->redirect(route('verification.notice'));
    }


    public function nextStep()
    {
        $this->validateStep();

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function updatedPin($value)
    {
        // Limita só números e até 6 dígitos
        $this->pin = preg_replace('/\D/', '', substr($value, 0, 6));
    }


    public function previousStep()
    {
        $this->step--;
    }

    public function submitStep()
    {
        if ($this->step < 3) {
            $this->nextStep();
        } else {
            $this->register();
        }
    }

    protected function validateStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            ]);
        } elseif ($this->step === 2) {
            $this->validate([
                'pin' => ['required', 'digits:6'],
            ]);
        } elseif ($this->step === 3) {
            $this->validate([
                'pin_confirmation' => ['required', 'digits:6', 'same:pin'],
            ]);
        }
    }
}
