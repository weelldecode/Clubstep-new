<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Payment;
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
        "paid_at" => null,
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
                    "approved",
                    "in_process",
                    "authorized",
                    "rejected",
                    "cancelled",
                    "canceled",
                    "refunded",
                    "charged_back",
                    "failed",
                ]),
            ],
            "statusForm.paid_at" => ["nullable", "date"],
        ];
    }

    public function openStatusModal(int $id): void
    {
        if (!$this->billingTablesReady()) {
            $this->setupError = "Tabela de pagamentos não encontrada. Rode as migrations.";
            return;
        }

        $this->resetErrorBag();
        $this->editingId = $id;

        $payment = Payment::findOrFail($id);

        $this->statusForm = [
            "status" => (string) $payment->status,
            "paid_at" => $payment->paid_at?->format("Y-m-d"),
        ];

        $this->showStatusModal = true;
    }

    public function saveStatus(): void
    {
        if (!$this->billingTablesReady()) {
            $this->addError("statusForm.status", "Tabela de pagamentos não encontrada.");
            return;
        }

        $this->validate();

        if (!$this->editingId) {
            return;
        }

        $payment = Payment::findOrFail($this->editingId);
        $payment->update([
            "status" => $this->statusForm["status"],
            "paid_at" => $this->statusForm["paid_at"] ?: null,
        ]);

        $this->showStatusModal = false;
        $this->dispatch("notify", message: "Pagamento atualizado.");
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

        $allowedSorts = ["amount", "status", "paid_at", "created_at"];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        if ($this->billingTablesReady()) {
            $payments = Payment::query()
                ->with([
                    "subscription:id,user_id,plan_id,status",
                    "subscription.user:id,name,email",
                    "subscription.plan:id,name",
                    "order:id,user_id,status,total_amount",
                    "order.user:id,name,email",
                ])
                ->when($this->search !== "", function ($q) {
                    $search = $this->search;
                    $q->where(function ($qq) use ($search) {
                        $qq->where("status", "like", "%{$search}%")
                            ->orWhere("payment_id_mercadopago", "like", "%{$search}%")
                            ->orWhere("order_id", "like", "%{$search}%")
                            ->orWhereHas("subscription.user", fn($uq) => $uq
                                ->where("name", "like", "%{$search}%")
                                ->orWhere("email", "like", "%{$search}%"))
                            ->orWhereHas("order.user", fn($oq) => $oq
                                ->where("name", "like", "%{$search}%")
                                ->orWhere("email", "like", "%{$search}%"))
                            ->orWhereHas("subscription.plan", fn($pq) => $pq
                                ->where("name", "like", "%{$search}%"));
                    });
                })
                ->orderBy($sortField, $sortDir)
                ->paginate(15)
                ->withQueryString();
        } else {
            $this->setupError = "Tabela de pagamentos não encontrada. Rode as migrations de billing.";
            $payments = new LengthAwarePaginator([], 0, 15, $this->getPage());
        }

        $statusOptions = [
            ["id" => "pending", "name" => "Pending"],
            ["id" => "approved", "name" => "Approved"],
            ["id" => "in_process", "name" => "In Process"],
            ["id" => "authorized", "name" => "Authorized"],
            ["id" => "rejected", "name" => "Rejected"],
            ["id" => "cancelled", "name" => "Cancelled"],
            ["id" => "canceled", "name" => "Canceled"],
            ["id" => "refunded", "name" => "Refunded"],
            ["id" => "charged_back", "name" => "Charged Back"],
            ["id" => "failed", "name" => "Failed"],
        ];

        return view("livewire.admin.payments.index", [
            "payments" => $payments,
            "statusOptions" => $statusOptions,
        ])->layout("layouts.admin.app");
    }

    private function billingTablesReady(): bool
    {
        return (Schema::hasTable("payments") || Schema::hasTable("payment")) &&
            (Schema::hasTable("subscriptions") || Schema::hasTable("subscription")) &&
            (Schema::hasTable("plans") || Schema::hasTable("plan"));
    }
}
