<?php

namespace App\Livewire\App\Studio;

use App\Models\Category;
use App\Models\Tag;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout("components.layouts.studio")]
class Dashboard extends Component
{

    use WithFileUploads;


    public $name;
    public $description;
    public $files = [];  // Para múltiplos arquivos
    public $maxItems = 5; // limite de arquivos


    public $tags = [];          // tags selecionadas
    public $category = null;   // único id
    public $availableTags = [];
    public $availableCategories = [];

    public function mount()
    {
        $this->availableTags = Tag::all()->pluck('name')->toArray();
        $this->availableCategories = Category::orderBy('name')->get();
    }

    public function toggleTag($tag)
    {
        if (in_array($tag, $this->tags)) {
            $this->tags = array_filter($this->tags, fn($t) => $t !== $tag);
        } else {
            $this->tags[] = $tag;
        }
        $this->tags = array_values($this->tags);
    }


    public function setCategory($categoryId)
    {
        if ($this->category === (int) $categoryId) {
            $this->category = null;
        } else {
            $this->category = (int) $categoryId;
        }
    }

    public function updatedFiles($newFiles)
    {
        foreach ($newFiles as $file) {
            // adiciona apenas se ainda não estiver no array
            if (!in_array($file, $this->files, true)) {
                $this->files[] = $file;
            }
        }

        // Limita a quantidade máxima
        if (count($this->files) > $this->maxItems) {
            session()->flash('message', "Você só pode adicionar até {$this->maxItems} itens.");
            $this->files = array_slice($this->files, 0, $this->maxItems);
        }
    }

    public function removeFile($index)
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files); // reindexa
    }

    public function save()
    {
        $this->validate([
            'files.*' => 'required|file|mimes:zip|max:30720', // 30MB por ZIP
            'tag' => 'nullable|integer|exists:tags,id',
            'category' => 'nullable|integer|exists:categories,id',
        ]);

        foreach ($this->files as $file) {
            $file->store('uploads', 'public');
        }

        session()->flash('message', 'Arquivos enviados com sucesso!');
        $this->files = []; // limpa os arquivos após salvar
    }
}
