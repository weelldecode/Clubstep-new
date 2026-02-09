<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = "";
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public bool $showModal = false;
    public ?int $editingId = null;

    public array $form = [
        "name" => "",
        "email" => "",
        "role" => "",
        "type" => "",
        "locale" => "pt_BR",
        "email_verified" => false,
    ];

    protected $queryString = [
        "search" => ["except" => ""],
        "sortField" => ["except" => "created_at"],
        "sortDirection" => ["except" => "desc"],
        "page" => ["except" => 1],
    ];

    public function updated($name): void
    {
        if (in_array($name, ["search", "sortField", "sortDirection"], true)) {
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        $rules = [
            "form.name" => ["required", "string", "max:255"],
            "form.email" => [
                "required",
                "string",
                "lowercase",
                "email",
                "max:255",
                Rule::unique("users", "email")->ignore($this->editingId),
            ],
        ];

        if ($this->hasColumn("role")) {
            $rules["form.role"] = ["nullable", "string", "max:50"];
        }

        if ($this->hasColumn("type")) {
            $rules["form.type"] = ["nullable", "string", "max:50"];
        }

        if ($this->hasColumn("locale")) {
            $available = array_keys($this->resolveLocaleOptions());
            $rules["form.locale"] = ["required", "string", Rule::in($available)];
        }

        if ($this->hasColumn("email_verified_at")) {
            $rules["form.email_verified"] = ["boolean"];
        }

        return $rules;
    }

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $user = User::findOrFail($id);

        $this->form = [
            "name" => $user->name,
            "email" => $user->email,
            "role" => $this->hasColumn("role") ? (string) ($user->role ?? "") : "",
            "type" => $this->hasColumn("type") ? (string) ($user->type ?? "") : "",
            "locale" => $this->hasColumn("locale") ? (string) ($user->locale ?? "pt_BR") : "pt_BR",
            "email_verified" => $this->hasColumn("email_verified_at") ? !empty($user->email_verified_at) : false,
        ];

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $user = User::findOrFail($this->editingId);

        $payload = [
            "name" => $this->form["name"],
            "email" => $this->form["email"],
        ];

        if ($this->hasColumn("role")) {
            $payload["role"] = $this->form["role"] ?: null;
        }

        if ($this->hasColumn("type")) {
            $payload["type"] = $this->form["type"] ?: null;
        }

        if ($this->hasColumn("locale")) {
            $payload["locale"] = $this->form["locale"] ?: "pt_BR";
        }

        if ($this->hasColumn("email_verified_at")) {
            $payload["email_verified_at"] = $this->form["email_verified"] ? now() : null;
        }

        $user->update($payload);

        $this->showModal = false;
        $this->dispatch("notify", message: "Usuario atualizado.");
    }

    public function toggleSort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === "asc" ? "desc" : "asc";
            return;
        }

        $this->sortField = $field;
        $this->sortDirection = "asc";
    }

    private function hasColumn(string $column): bool
    {
        return Schema::hasColumn("users", $column);
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

    public function render()
    {
        $allowedSorts = ["name", "email", "created_at"];
        if ($this->hasColumn("role")) {
            $allowedSorts[] = "role";
        }
        if ($this->hasColumn("type")) {
            $allowedSorts[] = "type";
        }
        if ($this->hasColumn("locale")) {
            $allowedSorts[] = "locale";
        }

        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        $users = User::query()
            ->when(
                $this->search !== "",
                fn($q) => $q->where(function ($qq) {
                    $qq->where("name", "like", "%{$this->search}%")
                        ->orWhere("email", "like", "%{$this->search}%");
                }),
            )
            ->orderBy($sortField, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $roleOptions = [];
        if ($this->hasColumn("role")) {
            $roleOptions = User::query()
                ->select("role")
                ->whereNotNull("role")
                ->distinct()
                ->pluck("role")
                ->filter()
                ->values()
                ->map(fn($role) => ["id" => $role, "name" => ucfirst((string) $role)])
                ->toArray();

            if (empty($roleOptions)) {
                $roleOptions = [
                    ["id" => "admin", "name" => "Admin"],
                    ["id" => "verified", "name" => "Verified"],
                    ["id" => "user", "name" => "User"],
                ];
            }
        }

        $typeOptions = [];
        if ($this->hasColumn("type")) {
            $typeOptions = User::query()
                ->select("type")
                ->whereNotNull("type")
                ->distinct()
                ->pluck("type")
                ->filter()
                ->values()
                ->map(fn($type) => ["id" => $type, "name" => ucfirst((string) $type)])
                ->toArray();

            if (empty($typeOptions)) {
                $typeOptions = [
                    ["id" => "verified", "name" => "Verified"],
                    ["id" => "user", "name" => "User"],
                ];
            }
        }

        $localeOptions = [];
        if ($this->hasColumn("locale")) {
            $localeOptions = collect($this->resolveLocaleOptions())
                ->map(fn($label, $value) => ["id" => $value, "name" => $label])
                ->values()
                ->toArray();
        }

        return view("livewire.admin.users.index", [
            "users" => $users,
            "hasRole" => $this->hasColumn("role"),
            "hasType" => $this->hasColumn("type"),
            "hasLocale" => $this->hasColumn("locale"),
            "hasEmailVerified" => $this->hasColumn("email_verified_at"),
            "roleOptions" => $roleOptions,
            "typeOptions" => $typeOptions,
            "localeOptions" => $localeOptions,
        ])->layout("layouts.admin.app");
    }
}
