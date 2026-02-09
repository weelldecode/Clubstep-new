<?php

namespace App\Livewire\App;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domain\Home\Queries\HomeFeedQuery;
use App\Domain\Users\Queries\FollowingIdsQuery;

class Home extends Component
{
    public $recomendadas;
    public $dosSeguidos;
    public $categorias;
    public $categories;
    public $featuredArtists;
    public $bestSellers;

    public $perPage = 20;

    public function loadMore()
    {
        $this->perPage += 20;
    }

    public function mount(
        HomeFeedQuery $homeFeed,
        FollowingIdsQuery $followingIds,
    ) {
        $user = Auth::user();

        $ids = $user ? $followingIds->run($user) : [];

        $data = $homeFeed->run($user?->id, $ids);

        $this->recomendadas = $data["recomendadas"];
        $this->featuredArtists = $data["featuredArtists"];
        $this->categories = $data["categories"];
        $this->categorias = $data["categorias"];
        $this->bestSellers = $data["bestSellers"];
        $this->dosSeguidos = $data["dosSeguidos"];
    }

    public function render()
    {
        return view("livewire.app.home")
            ->title("Pagina Inicial")
            ->layout("layouts.app");
    }
}
