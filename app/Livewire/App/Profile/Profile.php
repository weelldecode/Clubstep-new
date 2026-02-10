<?php

namespace App\Livewire\App\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;
    public User $user;
    public ?bool $isFollowing = null;

    public $banner;
    public $avatar;
    public $bannerTemp;
    public $avatarTemp;

    protected $rules = [
        "bannerTemp" => "image|max:5120", // até 5MB
        "avatarTemp" => "image|max:2048", // até 2MB
    ];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->isFollowing = Auth::user()?->following->contains($user);
    }

    public function updatedBannerTemp()
    {
        $this->validate(["bannerTemp" => "image|max:5120"]);
        $path = $this->bannerTemp->store("banners", "public");
        $this->user->profile_banner = $path;
        $this->user->save();
        $this->bannerTemp = null;
        $this->dispatch(
            "notify",
            message: "Banner alterado com sucesso!",
            type: "success",
        );
    }

    public function updatedAvatarTemp()
    {
        $this->validate(["avatarTemp" => "image|max:2048"]);
        $path = $this->avatarTemp->store("avatars", "public");
        $this->user->profile_image = $path;
        $this->user->save();
        $this->avatarTemp = null;
        $this->dispatch(
            "notify",
            message: "Avatar alterado com sucesso!",
            type: "success",
        );
    }

    public function toggleFollow()
    {
        $authUser = auth()->user();

        if ($this->isFollowing) {
            $authUser->following()->detach($this->user->id);
            $this->isFollowing = false;

            $this->dispatch(
                "notify",
                message: "Você deixou de seguir {$this->user->name}",
                type: "error",
            );
        } else {
            $authUser->following()->attach($this->user->id);
            $this->isFollowing = true;

            $this->dispatch(
                "notify",
                message: "Você agora segue {$this->user->name}",
                type: "success",
            );
        }
    }

    public function render()
    {
        return view("livewire.app.profile.profile", [
            "collections" => $this->user->collections()->latest()->get(),
            "followersCount" => $this->user->followers()->count(),
            "followingCount" => $this->user->following()->count(),
            "seoTitle" => $this->user->name,
            "seoDescription" => t("Discover collections and creations by :name.", [
                "name" => $this->user->name,
            ]),
            "seoImage" => $this->user->profile_image
                ? asset("storage/" . $this->user->profile_image)
                : null,
        ])
            ->title($this->user->name)
            ->layout("layouts.app");
    }
}
