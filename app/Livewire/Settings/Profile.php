<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ProfileRingStyle;

class Profile extends Component
{
    use WithFileUploads;
    public string $name = "";

    public string $email = "";

    public $photo;
    public bool $profileAnimationsEnabled = true;

    public string $locale = "pt_BR";

    public array $localeOptions = [];
    public $profileRingStyleId = null;
    public $profileRingStyles = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->profileAnimationsEnabled = (bool) $user->profile_animations_enabled;

        $this->localeOptions = $this->resolveLocaleOptions();
        $this->locale = $user->locale ?: app()->getLocale();
        $this->profileRingStyleId = $user->profile_ring_style_id;
        if ($user->type === "verified") {
            $this->profileRingStyles = ProfileRingStyle::query()
                ->orderBy("name")
                ->get();
        }
    }

    public function render()
    {
        return view("livewire.settings.profile")
            ->title("Pagina Inicial")
            ->layout("layouts.app");
    }
    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $availableLocales = array_keys($this->resolveLocaleOptions());

        $validated = $this->validate([
            "name" => ["required", "string", "max:255"],

            "email" => [
                "required",
                "string",
                "lowercase",
                "email",
                "max:255",
                Rule::unique(User::class)->ignore($user->id),
            ],
            "locale" => ["required", "string", Rule::in($availableLocales)],
            "photo" => ["nullable", "image", "max:2048"], // máx 2MB
            "profileAnimationsEnabled" => ["boolean"],
            "profileRingStyleId" => [
                "nullable",
                "integer",
                Rule::exists("profile_ring_styles", "id"),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty("email")) {
            $user->email_verified_at = null;
        }

        // Upload da foto
        if (isset($validated["photo"]) && $validated["photo"]) {
            $mime = $validated["photo"]->getMimeType();
            $isGif = $mime && str_contains($mime, "gif");
            $canUseAnimations =
                $user->type === "verified" &&
                (bool) $validated["profileAnimationsEnabled"];

            if ($isGif && !$canUseAnimations) {
                $this->addError(
                    "photo",
                    t("Animated avatars are available for verified profiles only."),
                );
                return;
            }
            // Deleta foto antiga, se existir
            if (
                $user->profile_image &&
                Storage::disk("public")->exists($user->profile_image)
            ) {
                Storage::disk("public")->delete($user->profile_image);
            }
            if ($user->profile_image && preg_match("/\\.gif$/i", $user->profile_image)) {
                $staticPath = preg_replace("/\\.gif$/i", ".png", $user->profile_image);
                if (Storage::disk("public")->exists($staticPath)) {
                    Storage::disk("public")->delete($staticPath);
                }
            }

            $path = $validated["photo"]->store("profile_images", "public");
            $user->profile_image = $path;
            if ($isGif && $canUseAnimations && function_exists("imagecreatefromgif")) {
                $fullPath = Storage::disk("public")->path($path);
                $image = @imagecreatefromgif($fullPath);
                if ($image) {
                    $staticPath = Storage::disk("public")->path(
                        preg_replace("/\\.gif$/i", ".png", $path),
                    );
                    imagepng($image, $staticPath, 6);
                    imagedestroy($image);
                }
            }
        }

        if ($user->type === "verified") {
            $user->profile_animations_enabled = (bool) $validated[
                "profileAnimationsEnabled"
            ];
            $user->profile_ring_style_id = $validated["profileRingStyleId"];
        }

        $user->save();

        if (
            $user->type === "verified" &&
            !$user->profile_animations_enabled &&
            $user->profile_image &&
            preg_match("/\\.gif$/i", $user->profile_image) &&
            function_exists("imagecreatefromgif")
        ) {
            $staticPath = preg_replace(
                "/\\.gif$/i",
                ".png",
                $user->profile_image,
            );
            if (!Storage::disk("public")->exists($staticPath)) {
                $fullPath = Storage::disk("public")->path(
                    $user->profile_image,
                );
                $image = @imagecreatefromgif($fullPath);
                if ($image) {
                    $savePath = Storage::disk("public")->path($staticPath);
                    imagepng($image, $savePath, 6);
                    imagedestroy($image);
                }
            }
        }

        if (
            $user->type === "verified" &&
            !$user->profile_animations_enabled &&
            $user->profile_banner &&
            preg_match("/\\.gif$/i", $user->profile_banner) &&
            function_exists("imagecreatefromgif")
        ) {
            $staticPath = preg_replace(
                "/\\.gif$/i",
                ".png",
                $user->profile_banner,
            );
            if (!Storage::disk("public")->exists($staticPath)) {
                $fullPath = Storage::disk("public")->path(
                    $user->profile_banner,
                );
                $image = @imagecreatefromgif($fullPath);
                if ($image) {
                    $savePath = Storage::disk("public")->path($staticPath);
                    imagepng($image, $savePath, 6);
                    imagedestroy($image);
                }
            }
        }
        app()->setLocale($user->locale);

        $this->dispatch("profile-updated", name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(
                default: route("dashboard", absolute: false),
            );

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash("status", "verification-link-sent");
    }

    private function resolveLocaleOptions(): array
    {
        $supported = config("laravellocalization.supportedLocales", []);
        $options = [];

        foreach ($supported as $key => $meta) {
            $options[$key] = $meta["native"] ?? $meta["name"] ?? $key;
        }

        if (empty($options)) {
            $options = [
                "pt_BR" => "português do Brasil",
                "en" => "English",
            ];
        }

        return $options;
    }
}
