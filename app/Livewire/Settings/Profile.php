<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;
    public string $name = "";

    public string $email = "";

    public $photo;

    public string $locale = "pt_BR";

    public array $localeOptions = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        $this->localeOptions = $this->resolveLocaleOptions();
        $this->locale = $user->locale ?: app()->getLocale();
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
        ]);

        $user->fill($validated);

        if ($user->isDirty("email")) {
            $user->email_verified_at = null;
        }

        // Upload da foto
        if (isset($validated["photo"]) && $validated["photo"]) {
            // Deleta foto antiga, se existir
            if (
                $user->profile_image &&
                Storage::disk("public")->exists($user->profile_image)
            ) {
                Storage::disk("public")->delete($user->profile_image);
            }

            $path = $validated["photo"]->store("profile_images", "public");
            $user->profile_image = $path;
        }

        $user->save();
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
