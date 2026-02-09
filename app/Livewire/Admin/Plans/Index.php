<?php

namespace App\Livewire\Admin\Plans;

use App\Models\Plan;
use Illuminate\Pagination\LengthAwarePaginator;
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
        "slug" => "",
        "description" => "",
        "price" => 0,
        "limit_download" => 0,
        "features_text" => "",
    ];
    public ?string $setupError = null;

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
                Rule::unique("plans", "slug")->ignore($this->editingId),
            ],
            "form.description" => ["nullable", "string", "max:2000"],
            "form.price" => ["required", "numeric", "min:0"],
            "form.limit_download" => ["required", "integer", "min:0"],
            "form.features_text" => ["nullable", "string", "max:8000"],
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
            "price" => 0,
            "limit_download" => 0,
            "features_text" => "",
        ];
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        if (!$this->plansTableReady()) {
            $this->setupError = "Tabela de planos não encontrada. Rode as migrations.";
            return;
        }

        $this->resetErrorBag();
        $this->editingId = $id;

        $plan = Plan::findOrFail($id);

        $this->form = [
            "name" => $plan->name,
            "slug" => $plan->slug,
            "description" => $plan->description ?? "",
            "price" => (float) $plan->price,
            "limit_download" => (int) ($plan->limit_download ?? 0),
            "features_text" => collect($plan->features ?? [])->join("\n"),
        ];
        $this->showModal = true;
    }

    public function save(): void
    {
        if (!$this->plansTableReady()) {
            $this->addError("form.name", "Tabela de planos não encontrada.");
            return;
        }

        $this->validate();

        $features = collect(
            preg_split("/\r\n|\r|\n/", (string) ($this->form["features_text"] ?? "")),
        )
            ->map(fn($f) => trim((string) $f))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $payload = [
            "name" => $this->form["name"],
            "slug" => $this->form["slug"],
            "description" => $this->form["description"] ?: null,
            "price" => $this->form["price"],
            "limit_download" => $this->form["limit_download"],
            "features" => $features,
        ];

        if ($this->editingId) {
            Plan::findOrFail($this->editingId)->update($payload);
        } else {
            Plan::create($payload);
        }

        $this->showModal = false;
        $this->dispatch("notify", message: "Plano salvo com sucesso.");
    }

    public function delete(int $id): void
    {
        if (!$this->plansTableReady()) {
            $this->setupError = "Tabela de planos não encontrada.";
            return;
        }

        try {
            Plan::whereKey($id)->delete();
            $this->dispatch("notify", message: "Plano removido.");
        } catch (\Throwable $e) {
            $this->dispatch("notify", message: "Nao foi possivel remover o plano.");
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

    public function render()
    {
        $this->setupError = null;

        $allowedSorts = ["name", "slug", "price", "limit_download", "created_at"];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        if ($this->plansTableReady()) {
            $plans = Plan::query()
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
        } else {
            $this->setupError = "Tabela de planos não encontrada. Rode as migrations de billing.";
            $plans = new LengthAwarePaginator([], 0, 15, $this->getPage());
        }

        return view("livewire.admin.plans.index", [
            "plans" => $plans,
        ])->layout("layouts.admin.app");
    }

    private function plansTableReady(): bool
    {
        return Schema::hasTable("plans") || Schema::hasTable("plan");
    }
}
