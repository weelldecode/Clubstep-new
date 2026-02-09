<?php

namespace App\Livewire\Admin\Tags;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
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
        "slug" => "",
        "description" => "",
        "type" => "collection",
        "parent_id" => null,
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
                Rule::unique("tags", "slug")->ignore($this->editingId),
            ],
            "form.description" => ["nullable", "string", "max:2000"],
            "form.type" => ["required", Rule::in(["collection", "item"])],
            "form.parent_id" => ["nullable", "integer", "exists:tags,id"],
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
        ];
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $tag = Tag::findOrFail($id);
        $this->form = [
            "name" => $tag->name,
            "slug" => $tag->slug,
            "description" => $tag->description ?? "",
            "type" => $tag->type ?? "collection",
            "parent_id" => $tag->parent_id,
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId && (int) $this->form["parent_id"] === $this->editingId) {
            $this->addError("form.parent_id", "A tag nao pode ser pai dela mesma.");
            return;
        }

        if ($this->editingId) {
            $tag = Tag::findOrFail($this->editingId);
            $tag->update($this->form);
        } else {
            Tag::create($this->form);
        }

        $this->flushTaxonomyCache();
        $this->showModal = false;
        $this->dispatch("notify", message: "Tag salva com sucesso.");
    }

    public function delete(int $id): void
    {
        try {
            Tag::whereKey($id)->delete();
            $this->flushTaxonomyCache();
            $this->dispatch("notify", message: "Tag removida.");
        } catch (\Throwable $e) {
            $this->dispatch("notify", message: "Nao foi possivel remover a tag.");
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
        Cache::forget("tax:v1:tags");
    }

    public function render()
    {
        $allowedSorts = ["name", "slug", "type", "created_at"];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        $tags = Tag::query()
            ->with("parent:id,name")
            ->withCount(["collections", "legacyCollections"])
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

        $parentOptions = Tag::query()
            ->when($this->editingId, fn($q) => $q->whereKeyNot($this->editingId))
            ->orderBy("name")
            ->get(["id", "name"])
            ->map(fn($t) => ["id" => $t->id, "name" => $t->name])
            ->toArray();

        $typeOptions = [
            ["id" => "collection", "name" => "Collection"],
            ["id" => "item", "name" => "Item"],
        ];

        return view("livewire.admin.tags.index", [
            "tags" => $tags,
            "parentOptions" => $parentOptions,
            "typeOptions" => $typeOptions,
        ])->layout("layouts.admin.app");
    }
}
