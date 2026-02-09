<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Visibility extends Component
{
    public $user;

    public $is_private;
    public $hide_collections;
    public $hide_followers;
    public $hide_following;

    public function rules(): array
    {
        return [
            "is_private" => "boolean",
            "hide_collections" => "boolean",
            "hide_followers" => "boolean",
            "hide_following" => "boolean",
        ];
    }
    public function mount(): void
    {
        $this->user = Auth::user();

        // Certifique-se de que está convertendo para boolean
        $this->is_private = (bool) $this->user->is_private;
        $this->hide_collections = (bool) $this->user->hide_collections;
        $this->hide_followers = (bool) $this->user->hide_followers;
        $this->hide_following = (bool) $this->user->hide_following;
    }

    /**
     * Atualiza a propriedade no banco assim que o switch mudar
     */
    public function updated($property)
    {
        $this->validateOnly($property);

        $this->user->{$property} = $this->{$property};
        $this->user->save();

        // Notificação opcional
        $this->dispatch(
            "notify",
            message: "Configuração atualizada!",
            type: "success",
        );
    }

    public function render()
    {
        return view("livewire.settings.visibility")->layout("layouts.app");
    }
}
