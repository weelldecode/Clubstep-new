<?php

namespace App\Livewire\App\Downloads;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Manager extends Component
{
    public $downloads;

    public function mount()
    {
        $this->downloads = Auth::user()
            ->downloads()
            ->with("collection")
            ->latest()
            ->get();
    }

    public function render()
    {
        return view("livewire.app.downloads.manager")
            ->title("Seus Downloads")
            ->layout("layouts.app");
    }
}
