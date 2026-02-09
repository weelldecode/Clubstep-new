<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class Appearance extends Component
{
    //

     public function render()
    {
        return view("livewire.settings.appearance")
            ->title("Pagina Inicial")
            ->layout("layouts.app");
    }
}
