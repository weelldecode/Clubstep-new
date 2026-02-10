<?php

namespace App\Livewire\App\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    private function isGif($file): bool
    {
        $mime = $file?->getMimeType();
        return $mime && str_contains($mime, "gif");
    }

    private function canUseAnimations(): bool
    {
        return $this->user->type === "verified" &&
            ($this->user->profile_animations_enabled ?? true);
    }

    private function staticPngPath(string $path): string
    {
        return preg_replace("/\\.gif$/i", ".png", $path);
    }

    private function deleteProfileAsset(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Storage::disk("public")->exists($path)) {
            Storage::disk("public")->delete($path);
        }

        if (preg_match("/\\.gif$/i", $path)) {
            $staticPath = $this->staticPngPath($path);
            if (Storage::disk("public")->exists($staticPath)) {
                Storage::disk("public")->delete($staticPath);
            }
        }
    }

    private function createStaticFromGif(string $path): void
    {
        if (!preg_match("/\\.gif$/i", $path)) {
            return;
        }

        if (!function_exists("imagecreatefromgif")) {
            return;
        }

        $fullPath = Storage::disk("public")->path($path);
        $image = @imagecreatefromgif($fullPath);
        if (!$image) {
            return;
        }

        $staticPath = Storage::disk("public")->path(
            $this->staticPngPath($path),
        );

        imagepng($image, $staticPath, 6);
        imagedestroy($image);
    }

    public function mount(User $user)
    {
        $this->user = $user;
        $this->isFollowing = Auth::user()?->following->contains($user);
    }

    public function updatedBannerTemp()
    {
        $this->validate(["bannerTemp" => "image|max:5120"]);
        if ($this->isGif($this->bannerTemp) && !$this->canUseAnimations()) {
            $this->addError(
                "bannerTemp",
                t("Animated banners are available for verified profiles only."),
            );
            $this->bannerTemp = null;
            return;
        }
        $this->deleteProfileAsset($this->user->profile_banner);
        $path = $this->bannerTemp->store("banners", "public");
        $this->user->profile_banner = $path;
        $this->user->save();
        if ($this->isGif($this->bannerTemp) && $this->canUseAnimations()) {
            $this->createStaticFromGif($path);
        }
        $this->bannerTemp = null;
        $this->dispatch(
            "profile-media-updated",
            banner: $this->user->bannerUrl(),
            avatar: $this->user->avatar()["value"] ?? null,
            ts: now()->timestamp,
        );
        $this->dispatch(
            "notify",
            message: "Banner alterado com sucesso!",
            type: "success",
        );
    }

    public function updatedAvatarTemp()
    {
        $this->validate(["avatarTemp" => "image|max:2048"]);
        if ($this->isGif($this->avatarTemp) && !$this->canUseAnimations()) {
            $this->addError(
                "avatarTemp",
                t("Animated avatars are available for verified profiles only."),
            );
            $this->avatarTemp = null;
            return;
        }
        $this->deleteProfileAsset($this->user->profile_image);
        $path = $this->avatarTemp->store("avatars", "public");
        $this->user->profile_image = $path;
        $this->user->save();
        if ($this->isGif($this->avatarTemp) && $this->canUseAnimations()) {
            $this->createStaticFromGif($path);
        }
        $this->avatarTemp = null;
        $this->dispatch(
            "profile-media-updated",
            banner: $this->user->bannerUrl(),
            avatar: $this->user->avatar()["value"] ?? null,
            ts: now()->timestamp,
        );
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
