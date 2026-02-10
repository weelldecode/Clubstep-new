<?php

namespace App\Livewire\Admin\Collections;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

use App\Models\Collection;
use App\Models\Item;

class Items extends Component
{
    use WithPagination, WithFileUploads;

    public Collection $collection;

    public string $search = "";
    public string $sortField = "created_at";
    public string $sortDirection = "desc";

    public bool $showModal = false;
    public ?int $editingId = null;
    public $imageFile = null; // arquivo enviado
    public $image_path = null; // capa -> image_path
    public array $galleryFiles = []; // galeria -> images[]
    public array $form = [
        "name" => "",
        "slug" => "",
        "type" => "mockups",
        "price" => 0,
        "file_url" => "",
        "image_path" => "", // string salva no banco
        "image_upload" => null, // TemporaryUploadedFile
        "file_upload" => null, // TemporaryUploadedFile (zip)
        "images" => [], // strings
        "gallery_uploads" => [], // TemporaryUploadedFile[]
    ];

    protected $queryString = [
        "search" => ["except" => ""],
        "sortField" => ["except" => "created_at"],
        "sortDirection" => ["except" => "desc"],
        "page" => ["except" => 1],
    ];

    public function mount(Collection $collection): void
    {
        $this->collection = $collection;
    }

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
            "form.name" => ["required", "string", "max:140"],
            "form.slug" => [
                "required",
                "string",
                "max:160",
                Rule::unique("items", "slug")->ignore($this->editingId),
            ],
            "form.type" => ["required", Rule::in(["mockups", "arts", "sites"])],
            "form.price" => ["required", "numeric", "min:0"],
            "form.file_url" => ["nullable", "string", "max:2048"],
            "form.file_upload" => ["nullable", "file", "mimes:zip", "max:51200"],
            "form.image_upload" => ["nullable", "image", "max:4096"],
            "form.gallery_uploads" => ["nullable", "array"],
            "form.gallery_uploads.*" => ["image", "max:4096"],
        ];
    }

    public function openCreate(): void
    {
        $this->resetErrorBag();

        $this->form = [
            "name" => "",
            "slug" => "",
            "type" => "mockups",
            "price" => 0,
            "file_url" => "",
            "image_path" => "",
            "images" => [],

            "image_upload" => null,
            "file_upload" => null,
            "gallery_uploads" => [],
        ];

        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $item = Item::where("collection_id", $this->collection->id)->findOrFail(
            $id,
        );

        $this->form = [
            "name" => $item->name,
            "slug" => $item->slug,
            "type" => $item->type ?? "mockups",
            "price" => $item->price ?? 0,
            "file_url" => $item->file_url ?? "",
            "image_path" => $item->image_path ?? "",
            "images" => $item->images ?? [],

            "image_upload" => null,
            "file_upload" => null,
            "gallery_uploads" => [],
        ];

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            "name" => $this->form["name"],
            "slug" => $this->form["slug"],
            "type" => $this->form["type"],
            "price" => $this->form["price"],
            "file_url" => $this->form["file_url"] ?? "",
            "image_path" => $this->form["image_path"] ?? "", // string existente
            "images" => is_array($this->form["images"] ?? null)
                ? $this->form["images"]
                : [],
        ];

        // ✅ arquivo zip (novo upload)
        if (!empty($this->form["file_upload"])) {
            $payload["file_url"] = $this->form["file_upload"]->store(
                "items/files",
                "public",
            );
        }

        // ✅ capa (novo upload)
        if (!empty($this->form["image_upload"])) {
            $payload["image_path"] = $this->form["image_upload"]->store(
                "items/covers",
                "public",
            );
        }

        // ✅ galeria (append)
        if (!empty($this->form["gallery_uploads"])) {
            foreach ($this->form["gallery_uploads"] as $file) {
                $payload["images"][] = $file->store("items/gallery", "public");
            }
        }

        $payload["images"] = array_values(array_filter($payload["images"]));

        if ($this->editingId) {
            $item = Item::where(
                "collection_id",
                $this->collection->id,
            )->findOrFail($this->editingId);
            $item->update($payload);
        } else {
            $payload["collection_id"] = $this->collection->id;
            Item::create($payload);
        }

        // ✅ limpar temporários (senão fica “preso” no state)
        $this->form["image_upload"] = null;
        $this->form["file_upload"] = null;
        $this->form["gallery_uploads"] = [];

        $this->showModal = false;
        $this->dispatch("notify", message: "Item salvo.");
    }

    public function removeGalleryImage(int $index): void
    {
        $imgs = is_array($this->form["images"] ?? null)
            ? $this->form["images"]
            : [];

        if (!array_key_exists($index, $imgs)) {
            return;
        }

        unset($imgs[$index]);
        $this->form["images"] = array_values($imgs);
    }

    public function delete(int $id): void
    {
        Item::where("collection_id", $this->collection->id)
            ->whereKey($id)
            ->delete();
        $this->dispatch("notify", message: "Item removido.");
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
        $allowedSorts = ["name", "slug", "created_at"];
        $sortField = in_array($this->sortField, $allowedSorts, true)
            ? $this->sortField
            : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        $items = Item::query()
            ->where("collection_id", $this->collection->id)
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
            ->paginate(20)
            ->withQueryString();

        return view("livewire.admin.collections.items", [
            "items" => $items,
        ])->layout("layouts.admin.app");
    }
}
