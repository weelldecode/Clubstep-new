<?php

namespace App\Livewire\Admin\Collections;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Collection;
use App\Models\Category;
use App\Models\Tag;
use App\Domain\Collections\Enums\CollectionVisibility;
use App\Domain\Collections\Enums\CollectionStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = "";
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public bool $showModal = false;
    public ?int $editingId = null;

    public string $userSearch = "";
    public array $userOptions = [];

    public array $form = [
        "name" => "",
        "slug" => "",
        "description" => "",
        "type" => "mockups",
        "file_url" => "",
        "visibility" => "public",
        "status" => "draft",
        "category_ids" => [],
        "tag_ids" => [],
        "file_upload" => null,
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

        if ($name === "form.name" && empty($this->form["slug"])) {
            $this->form["slug"] = str($this->form["name"])->slug()->toString();
        }
    }

    protected function rules(): array
    {
        return [
            "form.name" => ["required", "string", "max:120"],
            "form.slug" => [
                "required",
                "string",
                "max:140",
                Rule::unique("collections", "slug")->ignore($this->editingId),
            ],
            "form.description" => ["nullable", "string", "max:2000"],
            "form.type" => ["required", Rule::in(["mockups", "arts", "sites"])],
            "form.file_url" => ["nullable", "string", "max:2048"],
            "form.file_upload" => ["nullable", "file", "mimes:zip", "max:51200"],
            "form.visibility" => [
                "required",
                Rule::in(array_column(CollectionVisibility::cases(), "value")),
            ],
            "form.user_id" => ["required", "integer", "exists:users,id"],
            "form.status" => [
                "required",
                Rule::in(array_column(CollectionStatus::cases(), "value")),
            ],
            "form.category_ids" => ["nullable", "array"],
            "form.category_ids.*" => ["integer", "exists:categories,id"],
            "form.tag_ids" => ["nullable", "array"],
            "form.tag_ids.*" => ["integer", "exists:tags,id"],
        ];
    }
    private function loadUserOptions(): void
    {
        $this->userOptions = User::query()
            ->select(["id", "name", "email"])
            ->when($this->userSearch !== "", function ($q) {
                $s = $this->userSearch;
                $q->where("name", "like", "%{$s}%")->orWhere(
                    "email",
                    "like",
                    "%{$s}%",
                );
            })
            ->orderBy("name")
            ->limit(20)
            ->get()
            ->map(
                fn($u) => [
                    "id" => $u->id,
                    "name" => "{$u->name} ({$u->email})",
                ],
            )
            ->toArray();
    }

    public function openCreate(): void
    {
        $this->resetErrorBag();
        $this->editingId = null;
        $this->userSearch = "";
        $this->loadUserOptions();
        $this->form = [
            "name" => "",
            "slug" => "",
            "description" => "",
            "type" => "mockups",
            "file_url" => "",
            "file_upload" => null,
            "user_id" => null,
            "visibility" => CollectionVisibility::Public->value,
            "status" => CollectionStatus::Draft->value,
            "category_ids" => [],
            "tag_ids" => [],
        ];
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $c = Collection::findOrFail($id);
        $this->userSearch = "";
        $this->loadUserOptions();

        $this->form = [
            "name" => $c->name,
            "slug" => $c->slug,
            "description" => $c->description ?? "",
            "type" => $c->type ?? "mockups",
            "file_url" => $c->file_url ?? "",
            "file_upload" => null,
            "user_id" => $c->user_id,
            "visibility" =>
                $c->visibility instanceof \BackedEnum
                    ? $c->visibility->value
                    : (string) $c->visibility,
            "status" =>
                $c->status instanceof \BackedEnum
                    ? $c->status->value
                    : (string) $c->status,
            "category_ids" => $c->categories()
                ->pluck("categories.id")
                ->merge($c->legacyCategories()->pluck("categories.id"))
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->toArray(),
            "tag_ids" => $c->tags()
                ->pluck("tags.id")
                ->merge($c->legacyTags()->pluck("tags.id"))
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->toArray(),
        ];
        // garante que o autor atual apareça no select mesmo se não estiver nos 20 primeiros
        if (
            $c->user_id &&
            !collect($this->userOptions)->contains("id", $c->user_id)
        ) {
            $u = User::select(["id", "name", "email"])->find($c->user_id);
            if ($u) {
                array_unshift($this->userOptions, [
                    "id" => $u->id,
                    "name" => "{$u->name} ({$u->email})",
                ]);
            }
        }
        $this->showModal = true;
    }
    public function updatedUserSearch(): void
    {
        $this->loadUserOptions();
    }
    public function save(): void
    {
        $this->validate();

        $payload = [
            "name" => $this->form["name"],
            "slug" => $this->form["slug"],
            "description" => $this->form["description"] ?? null,
            "type" => $this->form["type"],
            "file_url" => $this->form["file_url"] ?? "",
            "user_id" => $this->form["user_id"] ?? null,
            "visibility" => $this->form["visibility"],
            "status" => $this->form["status"],
        ];

        if (!empty($this->form["file_upload"])) {
            $payload["file_url"] = $this->form["file_upload"]->store(
                "collections/files",
                "public",
            );
        }

        if ($this->editingId) {
            $c = Collection::findOrFail($this->editingId);
            $c->update($payload);
        } else {
            if (!$payload["user_id"]) {
                $payload["user_id"] = auth()->id();
            }
            $c = Collection::create($payload);
        }

        $categoryIds = collect($this->form["category_ids"] ?? [])
            ->filter(fn($id) => !is_null($id) && $id !== "")
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $c->categories()->sync($categoryIds->all());

        $tagIds = collect($this->form["tag_ids"] ?? [])
            ->filter(fn($id) => !is_null($id) && $id !== "")
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        if ($tagIds->isNotEmpty() && !Schema::hasTable("collection_tag")) {
            $this->addError(
                "form.tag_ids",
                "Tabela collection_tag não existe. Rode as migrations para habilitar tags em coleções.",
            );
            return;
        }

        $c->tags()->sync($tagIds->all());

        $this->showModal = false;
        $this->form["file_upload"] = null;
        $this->dispatch("notify", message: "Salvo com sucesso.");
    }

    public function delete(int $id): void
    {
        Collection::whereKey($id)->delete();
        $this->dispatch("notify", message: "Removido.");
    }

    public function toggleSort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection =
                $this->sortDirection === "asc" ? "desc" : "asc";
            return;
        }

        $this->sortField = $field;
        $this->sortDirection = "asc";
    }

    public function render()
    {
        $allowedSorts = ["name", "slug", "status", "visibility", "created_at"];
        $sortField = in_array($this->sortField, $allowedSorts, true)
            ? $this->sortField
            : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        $collections = Collection::query()
            ->withCount("items")
            ->when(
                $this->search !== "",
                fn($q) => $q->where(function ($qq) {
                    $qq->where("name", "like", "%{$this->search}%")->orWhere(
                        "slug",
                        "like",
                        "%{$this->search}%",
                    );
                }),
            )
            ->orderBy($sortField, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $categoryOptions = Category::query()
            ->select(["id", "name"])
            ->where(function ($q) {
                $q->where("type", "collection")->orWhereNull("type");
            })
            ->orderBy("name")
            ->get();

        $tagOptions = Tag::query()
            ->select(["id", "name"])
            ->where(function ($q) {
                $q->where("type", "collection")->orWhereNull("type");
            })
            ->orderBy("name")
            ->get();

        $statusOptions = collect(CollectionStatus::cases())
            ->map(
                fn($case) => [
                    "id" => $case->value,
                    "name" => Str::headline($case->value),
                ],
            )
            ->toArray();

        $visibilityOptions = collect(CollectionVisibility::cases())
            ->map(
                fn($case) => [
                    "id" => $case->value,
                    "name" => Str::headline($case->value),
                ],
            )
            ->toArray();
        return view("livewire.admin.collections.index", [
            "collections" => $collections,
            "visibilityOptions" => $visibilityOptions,
            "statusOptions" => $statusOptions,
            "categoryOptions" => $categoryOptions,
            "tagOptions" => $tagOptions,
        ])->layout("layouts.admin.app");
    }
}
