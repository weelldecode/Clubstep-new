<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubscriptionModal extends Component
{

    public $showModal = false;

    public function mount()
    {
        $user = Auth::user();

        if ($user && $user->hasActiveSubscription() && !$user->subscription_modal_shown) {
            $this->showModal = true;
        }
    }

public function closeModal()
{
    $user = Auth::user();

    if ($user) {
        $user->subscription_modal_shown = true;
        $user->save();
    }

    $this->showModal = false;
}


    public function render()
    {
        return view('livewire.components.subscription-modal');
    }
}
