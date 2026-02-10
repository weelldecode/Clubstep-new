<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Spatie\Permission\Traits\HasRoles;
use App\Models\ProfileRingStyle;
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        "name",
        "email",
        "password",
        "pin",
        "profile_banner",
        "profile_image",
        "profile_animations_enabled",
        "profile_ring_style_id",
        "is_private",
        "hide_collections",
        "hide_followers",
        "hide_following",
        "locale",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ["pin", "remember_token"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "pin" => "hashed",
            "profile_animations_enabled" => "boolean",
        ];
    }

    public function avatar(): array
    {
        if ($this->profile_image) {
            $path = $this->profile_image;
            $animationsAllowed =
                $this->type === "verified" &&
                ($this->profile_animations_enabled ?? true);
            if (
                !$animationsAllowed &&
                str_ends_with(strtolower($path), ".gif")
            ) {
                $staticPath = preg_replace("/\\.gif$/i", ".png", $path);
                if (Storage::disk("public")->exists($staticPath)) {
                    $path = $staticPath;
                }
            }
            return [
                "type" => "image",
                "value" => asset("storage/" . $path),
            ];
        }

        return [
            "type" => "initials",
            "value" => Str::of($this->name)
                ->explode(" ")
                ->take(2)
                ->map(fn($word) => Str::substr($word, 0, 1))
                ->implode(""),
        ];
    }

    public function bannerUrl(): ?string
    {
        if (!$this->profile_banner) {
            return null;
        }

        $path = $this->profile_banner;
        $animationsAllowed =
            $this->type === "verified" &&
            ($this->profile_animations_enabled ?? true);

        if (
            !$animationsAllowed &&
            str_ends_with(strtolower($path), ".gif")
        ) {
            $staticPath = preg_replace("/\\.gif$/i", ".png", $path);
            if (Storage::disk("public")->exists($staticPath)) {
                $path = $staticPath;
            }
        }

        return asset("storage/" . $path);
    }

    public function profileRingStyle()
    {
        return $this->belongsTo(ProfileRingStyle::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(" ")
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode("");
    }

    public function isAdmin()
    {
        return $this->role === "admin";
    }

    public function isUser()
    {
        return $this->role === "user";
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasActiveSubscription()
    {
        return $this->subscriptions()->where("status", "active")->exists();
    }
    public function getActiveSubscriptionAttribute(): bool
    {
        return $this->subscriptions()
            ->where("status", "active")
            ->where(function ($query) {
                $query
                    ->whereNull("expires_at") // se não tiver expiração
                    ->orWhere("expires_at", ">", Carbon::now()); // ou não expirou ainda
            })
            ->exists();
    }
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where("status", "active")
            ->latest("expires_at");
    }

    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            "follows",
            "followed_id",
            "follower_id",
        );
    }

    public function following()
    {
        return $this->belongsToMany(
            User::class,
            "follows",
            "follower_id",
            "followed_id",
        );
    }
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Item::class, "favorites")->withTimestamps();
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function isOnline(): bool
    {
        return $this->last_seen_at &&
            $this->last_seen_at->gt(now()->subMinutes(5));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
