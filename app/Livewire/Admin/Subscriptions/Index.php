<?php

namespace App\Livewire\Admin\Subscriptions;

use App\Models\Subscription;
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
        "status" => "pending",
        "started_at" => null,
        "expires_at" => null,
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
                Rule::in([
                    "pending",
                    "active",
                    "expired",
                    "canceled",
                    "cancelled",
                    "paused",
                    "failed",
                ]),
            ],
            "statusForm.started_at" => ["nullable", "date"],
            "statusForm.expires_at" => ["nullable", "date", "after_or_equal:statusForm.started_at"],
        ];
    }

    public function openStatusModal(int $id): void
    {
        if (!$this->billingTablesReady()) {
            $this->setupError = "Tabela de assinaturas não encontrada. Rode as migrations.";
            return;
        }

        $this->resetErrorBag();
        $this->editingId = $id;

        $subscription = Subscription::findOrFail($id);

        $this->statusForm = [
            "status" => (string) $subscription->status,
            "started_at" => $subscription->started_at?->format("Y-m-d"),
            "expires_at" => $subscription->expires_at?->format("Y-m-d"),
        ];

        $this->showStatusModal = true;
    }

    public function saveStatus(): void
    {
        if (!$this->billingTablesReady()) {
            $this->addError("statusForm.status", "Tabela de assinaturas não encontrada.");
            return;
        }

        $this->validate();

        if (!$this->editingId) {
            return;
        }

        $subscription = Subscription::findOrFail($this->editingId);
        $subscription->update([
            "status" => $this->statusForm["status"],
            "started_at" => $this->statusForm["started_at"] ?: null,
            "expires_at" => $this->statusForm["expires_at"] ?: null,
        ]);

        $this->showStatusModal = false;
        $this->dispatch("notify", message: "Assinatura atualizada.");
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

        $allowedSorts = ["status", "started_at", "expires_at", "created_at"];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        if ($this->billingTablesReady()) {
            $subscriptions = Subscription::query()
                ->with(["user:id,name,email", "plan:id,name"])
                ->withCount("payments")
                ->when($this->search !== "", function ($q) {
                    $search = $this->search;
                    $q->where(function ($qq) use ($search) {
                        $qq->where("status", "like", "%{$search}%")
                            ->orWhereHas("user", fn($uq) => $uq
                                ->where("name", "like", "%{$search}%")
                                ->orWhere("email", "like", "%{$search}%"))
                            ->orWhereHas("plan", fn($pq) => $pq
                                ->where("name", "like", "%{$search}%"));
                    });
                })
                ->orderBy($sortField, $sortDir)
                ->paginate(15)
                ->withQueryString();
        } else {
            $this->setupError = "Tabela de assinaturas não encontrada. Rode as migrations de billing.";
            $subscriptions = new LengthAwarePaginator([], 0, 15, $this->getPage());
        }

        $statusOptions = [
            ["id" => "pending", "name" => "Pending"],
            ["id" => "active", "name" => "Active"],
            ["id" => "expired", "name" => "Expired"],
            ["id" => "canceled", "name" => "Canceled"],
            ["id" => "cancelled", "name" => "Cancelled"],
            ["id" => "paused", "name" => "Paused"],
            ["id" => "failed", "name" => "Failed"],
        ];

        return view("livewire.admin.subscriptions.index", [
            "subscriptions" => $subscriptions,
            "statusOptions" => $statusOptions,
        ])->layout("layouts.admin.app");
    }

    private function billingTablesReady(): bool
    {
        return (Schema::hasTable("subscriptions") || Schema::hasTable("subscription")) &&
            (Schema::hasTable("plans") || Schema::hasTable("plan"));
    }
}
