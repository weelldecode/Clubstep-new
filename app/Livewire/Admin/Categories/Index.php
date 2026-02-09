<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = "";
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public bool $showModal = false;
    public ?int $editingId = null;

    public array $form = [
        "name" => "",
        "slug" => "",
        "description" => "",
        "type" => "collection",
        "parent_id" => null,
        "image" => "",
        "image_upload" => null,
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
                Rule::unique("categories", "slug")->ignore($this->editingId),
            ],
            "form.description" => ["nullable", "string", "max:2000"],
            "form.type" => ["required", Rule::in(["collection", "item"])],
            "form.parent_id" => ["nullable", "integer", "exists:categories,id"],
            "form.image_upload" => ["nullable", "image", "max:4096"],
        ];
    }

    public function openCreate(): void
    {
        $this->resetErrorBag();
        $this->editingId = null;
        $this->form = [
            "name" => "",
            "slug" => "",
            "description" => "",
            "type" => "collection",
            "parent_id" => null,
            "image" => "",
            "image_upload" => null,
        ];
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $category = Category::findOrFail($id);
        $this->form = [
            "name" => $category->name,
            "slug" => $category->slug,
            "description" => $category->description ?? "",
            "type" => $category->type ?? "collection",
            "parent_id" => $category->parent_id,
            "image" => $category->image ?? "",
            "image_upload" => null,
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId && (int) $this->form["parent_id"] === $this->editingId) {
            $this->addError("form.parent_id", "A categoria nao pode ser pai dela mesma.");
            return;
        }

        $payload = [
            "name" => $this->form["name"],
            "slug" => $this->form["slug"],
            "description" => $this->form["description"] ?? null,
            "type" => $this->form["type"],
            "parent_id" => $this->form["parent_id"] ?: null,
            "image" => $this->form["image"] ?? "",
        ];

        if ($this->editingId) {
            $category = Category::findOrFail($this->editingId);
            if (!empty($this->form["image_upload"])) {
                if (!empty($category->image) && Storage::disk("public")->exists($category->image)) {
                    Storage::disk("public")->delete($category->image);
                }
                $payload["image"] = $this->form["image_upload"]->store("categories", "public");
            }
            $category->update($payload);
        } else {
            if (!empty($this->form["image_upload"])) {
                $payload["image"] = $this->form["image_upload"]->store("categories", "public");
            }
            Category::create($payload);
        }

        $this->flushTaxonomyCache();
        $this->showModal = false;
        $this->dispatch("notify", message: "Categoria salva com sucesso.");
    }

    public function delete(int $id): void
    {
        try {
            Category::whereKey($id)->delete();
            $this->flushTaxonomyCache();
            $this->dispatch("notify", message: "Categoria removida.");
        } catch (\Throwable $e) {
            $this->dispatch("notify", message: "Nao foi possivel remover a categoria.");
        }
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

    private function flushTaxonomyCache(): void
    {
        // remove atuais + legadas
        Cache::forget("tax:v3:categories:collection");
        Cache::forget("tax:v3:categories:item");
        Cache::forget("tax:v1:categories:collection");
        Cache::forget("tax:v1:categories:item");
    }

    public function render()
    {
        $allowedSorts = ["name", "slug", "type", "created_at"];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        $categories = Category::query()
            ->with("parent:id,name")
            ->withCount("collections")
            ->when(
                $this->search !== "",
                fn($q) => $q->where(function ($qq) {
                    $qq->where("name", "like", "%{$this->search}%")
                        ->orWhere("slug", "like", "%{$this->search}%");
                }),
            )
            ->orderBy($sortField, $sortDir)
            ->paginate(15)
            ->withQueryString();

        $parentOptions = Category::query()
            ->when($this->editingId, fn($q) => $q->whereKeyNot($this->editingId))
            ->orderBy("name")
            ->get(["id", "name"])
            ->map(fn($c) => ["id" => $c->id, "name" => $c->name])
            ->toArray();

        $typeOptions = [
            ["id" => "collection", "name" => "Collection"],
            ["id" => "item", "name" => "Item"],
        ];

        return view("livewire.admin.categories.index", [
            "categories" => $categories,
            "parentOptions" => $parentOptions,
            "typeOptions" => $typeOptions,
        ])->layout("layouts.admin.app");
    }
}
