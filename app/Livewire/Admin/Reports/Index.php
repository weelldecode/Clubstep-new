<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Report;
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

    public bool $showStatusModal = false;
    public ?int $editingId = null;

    public array $statusForm = [
        "status" => "open",
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
    }

    protected function rules(): array
    {
        return [
            "statusForm.status" => [
                "required",
                Rule::in(["open", "reviewing", "resolved", "rejected"]),
            ],
        ];
    }

    public function openStatusModal(int $id): void
    {
        if (!$this->reportsTableReady()) {
            $this->setupError = "Tabela de denúncias não encontrada. Rode as migrations.";
            return;
        }

        $this->resetErrorBag();
        $this->editingId = $id;

        $report = Report::findOrFail($id);

        $this->statusForm = [
            "status" => (string) $report->status,
        ];

        $this->showStatusModal = true;
    }

    public function saveStatus(): void
    {
        if (!$this->reportsTableReady()) {
            $this->addError("statusForm.status", "Tabela de denúncias não encontrada.");
            return;
        }

        $this->validate();

        if (!$this->editingId) {
            return;
        }

        $report = Report::findOrFail($this->editingId);
        $report->update([
            "status" => $this->statusForm["status"],
        ]);

        $this->showStatusModal = false;
        $this->dispatch("notify", message: "Denúncia atualizada.");
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

        $allowedSorts = ["status", "created_at", "reason"];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        if ($this->reportsTableReady()) {
            $reports = Report::query()
                ->with([
                    "user:id,name,email",
                    "item:id,name",
                ])
                ->when($this->search !== "", function ($q) {
                    $search = $this->search;
                    $q->where(function ($qq) use ($search) {
                        $qq->where("status", "like", "%{$search}%")
                            ->orWhere("reason", "like", "%{$search}%")
                            ->orWhereHas("user", fn($uq) => $uq
                                ->where("name", "like", "%{$search}%")
                                ->orWhere("email", "like", "%{$search}%"))
                            ->orWhereHas("item", fn($iq) => $iq
                                ->where("name", "like", "%{$search}%"));
                    });
                })
                ->orderBy($sortField, $sortDir)
                ->paginate(15)
                ->withQueryString();
        } else {
            $this->setupError = "Tabela de denúncias não encontrada. Rode as migrations.";
            $reports = new LengthAwarePaginator([], 0, 15, $this->getPage());
        }

        $statusOptions = [
            ["id" => "open", "name" => "Open"],
            ["id" => "reviewing", "name" => "Reviewing"],
            ["id" => "resolved", "name" => "Resolved"],
            ["id" => "rejected", "name" => "Rejected"],
        ];

        return view("livewire.admin.reports.index", [
            "reports" => $reports,
            "statusOptions" => $statusOptions,
        ])->layout("layouts.admin.app");
    }

    private function reportsTableReady(): bool
    {
        return Schema::hasTable("reports");
    }
}
