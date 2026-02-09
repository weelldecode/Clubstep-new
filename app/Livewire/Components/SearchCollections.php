<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Collection;
use App\Models\Category;
use Livewire\WithFileUploads;

class SearchCollections extends Component
{
    use WithFileUploads;
    public string $search = "";
    public ?int $selectedCategory = null;
    public $allCategories = [];
    public $results = [];
    public ?int $collectionType = null; // Por exemplo: tipo de coleção
    public $categories = []; // categorias dinâmicas
    public function mount()
    {
        $this->allCategories = Category::all();
    }

    public function updatedSearch()
    {
        $this->searchCollections();
    }

    // Atualiza categorias dinamicamente ao mudar o tipo
    public function updatedCollectionType($value)
    {
        $this->categories = Category::where("type", $value)->get();
        $this->selectedCategory = null; // Resetar seleção
    }
    public function updatedSelectedCategory()
    {
        $this->searchCollections();
    }

    public function searchCollections()
    {
        $query = Collection::query();

        if ($this->selectedCategory) {
            $query->whereHas("categories", function ($q) {
                $q->where("categories.id", $this->selectedCategory); // <- aqui
            });
        }
        if ($this->search) {
            $query->where("name", "like", "%" . $this->search . "%");
        }

        $this->results = $query->with("categories")->limit(10)->get();
    }

    // Redireciona para a página da coleção
    public function goToCollection($id)
    {
        return redirect()->route("collections.show", $id);
    }

    public function render()
    {
        return view("livewire.components.search-collections");
    }
}
