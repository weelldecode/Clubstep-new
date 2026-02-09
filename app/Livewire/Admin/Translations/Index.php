<?php

namespace App\Livewire\Admin\Translations;

use App\Models\Translation;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = "";
    public string $locale = "pt_BR";

    public bool $showModal = false;
    public ?int $editingId = null;

    public array $form = [
        "key" => "",
        "locale" => "pt_BR",
        "value" => "",
        "is_active" => true,
    ];

    protected $queryString = [
        "search" => ["except" => ""],
        "locale" => ["except" => "pt_BR"],
        "page" => ["except" => 1],
    ];

    public function updated($name): void
    {
        if (in_array($name, ["search", "locale"], true)) {
            $this->resetPage();
        }
    }

    protected function rules(): array
    {
        return [
            "form.key" => ["required", "string", "max:190"],
            "form.locale" => ["required", "string", "max:10"],
            "form.value" => ["required", "string"],
            "form.is_active" => ["boolean"],
        ];
    }

    public function openCreate(): void
    {
        $this->resetErrorBag();
        $this->editingId = null;
        $this->form = [
            "key" => "",
            "locale" => $this->locale ?: "pt_BR",
            "value" => "",
            "is_active" => true,
        ];
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetErrorBag();
        $this->editingId = $id;

        $row = Translation::findOrFail($id);

        $this->form = [
            "key" => $row->key,
            "locale" => $row->locale,
            "value" => $row->value,
            "is_active" => (bool) $row->is_active,
        ];

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            "key" => $this->form["key"],
            "locale" => $this->form["locale"],
            "value" => $this->form["value"],
            "is_active" => (bool) $this->form["is_active"],
        ];

        Translation::updateOrCreate(
            ["key" => $payload["key"], "locale" => $payload["locale"]],
            $payload,
        );

        $this->showModal = false;
        $this->dispatch("notify", message: "Tradução salva.");
    }

    public function render()
    {
        $query = Translation::query()
            ->when($this->locale !== "", fn($q) => $q->where("locale", $this->locale))
            ->when($this->search !== "", function ($q) {
                $search = $this->search;
                $q->where(function ($qq) use ($search) {
                    $qq->where("key", "like", "%{$search}%")
                        ->orWhere("value", "like", "%{$search}%");
                });
            })
            ->orderByDesc("updated_at");

        $translations = $query->paginate(20)->withQueryString();

        return view("livewire.admin.translations.index", [
            "translations" => $translations,
        ])->layout("layouts.admin.app");
    }
}
