<?php

namespace App\Livewire\Components;

use Livewire\Component;

class NotificationsDropdown extends Component
{
    public $notifications;

    public function mount()
    {
        $this->notifications = auth()
            ->user()
            ->unreadNotifications()
            ->take(10)
            ->get();
    }

    public function markAsRead($id)
    {
        auth()
            ->user()
            ->notifications()
            ->where("id", $id)
            ->update(["read_at" => now()]);
        $this->mount(); // recarrega
    }

    public function render()
    {
        return view("livewire.components.notification-dropdown");
    }
}
