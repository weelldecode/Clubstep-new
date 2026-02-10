<?php

namespace App\Livewire\App\Collection;

use Livewire\Component;
use Livewire\WithPagination;

use App\Models\Tag;
use App\Models\Category;

use App\Domain\Collections\DTOs\CollectionBrowseFilters;
use App\Domain\Collections\Queries\BrowseCollectionsQuery;
use App\Domain\Taxonomy\Queries\TaxonomyListsQuery;

class Index extends Component
{
    use WithPagination;

    public array $selectedCollectionCategories = [];
    public array $selectedItemCategories = []; // se quiser usar depois
    public array $selectedCollectionTags = [];
    public array $selectedItemTags = [];
    public array $selectedCollectionTypes = ["sites"];

    public string $search = "";
    public string $sortField = "name";
    public string $sortDirection = "asc";
    public string $viewMode = "card";

    // Model binding vindo das rotas /tag/{tag:slug} e /category/{category:slug}
    public ?Tag $tag = null;
    public ?Category $category = null;

    public $allCategories = [];
    public $allItemCategories = [];
    public $allTags = [];

    protected $queryString = [
        "search" => ["except" => ""],
        "selectedCollectionCategories" => ["except" => []],
        "selectedItemCategories" => ["except" => []],
        "selectedCollectionTags" => ["except" => []],
        "selectedItemTags" => ["except" => []],
        "selectedCollectionTypes" => ["except" => ["sites"]],
        "sortField" => ["except" => "name"],
        "sortDirection" => ["except" => "asc"],
        "page" => ["except" => 1],
    ];

    public function mount(?Tag $tag = null, ?Category $category = null): void
    {
        $this->tag = $tag;
        $this->category = $category;

        if (empty($this->selectedCollectionTypes)) {
            $this->selectedCollectionTypes = ["sites"];
        }

        // aplica filtros iniciais baseados na rota
        if ($this->tag) {
            $this->selectedCollectionTags = [$this->tag->id];
        }

        if ($this->category) {
            $this->selectedCollectionCategories = [$this->category->id];
        }
    }

    public function updated($name): void
    {
        // qualquer mudança de filtro → reseta paginação
        if (
            str_starts_with($name, "selected") ||
            $name === "search" ||
            $name === "sortField" ||
            $name === "sortDirection"
        ) {
            $this->resetPage();
        }
    }

    public function toggleSortDirection(): void
    {
        $this->sortDirection = $this->sortDirection === "asc" ? "desc" : "asc";
    }

    public function toggleViewMode(): void
    {
        $this->viewMode = $this->viewMode === "card" ? "list" : "card";
    }

    public function resetFilters(): void
    {
        $this->selectedCollectionCategories = [];
        $this->selectedItemCategories = [];
        $this->selectedCollectionTags = [];
        $this->selectedItemTags = [];
        $this->selectedCollectionTypes = ["sites"];
        $this->search = "";
        $this->sortField = "name";
        $this->sortDirection = "asc";
        $this->resetPage();
    }

    public function render(
        BrowseCollectionsQuery $browse,
        TaxonomyListsQuery $tax,
    ) {
        // carrega listas (cacheadas no Domain)
        $this->allCategories = $tax->collectionsCategories();
        $this->allItemCategories = $tax->itemsCategories();
        $this->allTags = $tax->tags();

        // garante que o usuário não "desfaça" o filtro da rota sem perceber:
        // se quiser permitir, remova estes 2 ifs.
        if ($this->tag && empty($this->selectedCollectionTags)) {
            $this->selectedCollectionTags = [$this->tag->id];
        }
        if ($this->category && empty($this->selectedCollectionCategories)) {
            $this->selectedCollectionCategories = [$this->category->id];
        }

        $filters = CollectionBrowseFilters::from([
            "search" => $this->search,
            "selectedCollectionTypes" => $this->selectedCollectionTypes,
            "selectedCollectionCategories" =>
                $this->selectedCollectionCategories,
            "selectedCollectionTags" => $this->selectedCollectionTags,
            "selectedItemTags" => $this->selectedItemTags,
            "sortField" => $this->sortField,
            "sortDirection" => $this->sortDirection,
        ]);

        $collections = $browse->build($filters)->paginate(9)->withQueryString();

        // título dinâmico
        $title = "Coleções";
        if ($this->tag) {
            $title = "Tag: " . $this->tag->name;
        }
        if ($this->category) {
            $title = "Categoria: " . $this->category->name;
        }

        return view("livewire.app.collections.index", compact("collections"))
            ->title($title)
            ->layout("layouts.app");
    }
}
