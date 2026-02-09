<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
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
    public bool $showItemsModal = false;
    public ?int $editingId = null;
    public ?int $itemsId = null;

    public array $statusForm = [
        "status" => "pending",
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
                Rule::in(["pending", "paid", "canceled"]),
            ],
        ];
    }

    public function openStatusModal(int $id): void
    {
        if (!$this->ordersTableReady()) {
            $this->setupError = "Tabela de pedidos não encontrada. Rode as migrations.";
            return;
        }

        $this->resetErrorBag();
        $this->editingId = $id;

        $order = Order::findOrFail($id);

        $this->statusForm = [
            "status" => (string) $order->status,
        ];

        $this->showStatusModal = true;
    }

    public function saveStatus(): void
    {
        if (!$this->ordersTableReady()) {
            $this->addError("statusForm.status", "Tabela de pedidos não encontrada.");
            return;
        }

        $this->validate();

        if (!$this->editingId) {
            return;
        }

        $order = Order::findOrFail($this->editingId);
        $order->update([
            "status" => $this->statusForm["status"],
        ]);

        $this->showStatusModal = false;
        $this->dispatch("notify", message: "Pedido atualizado.");
    }

    public function openItemsModal(int $id): void
    {
        $this->itemsId = $id;
        $this->showItemsModal = true;
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

        $allowedSorts = ["total_amount", "status", "created_at"];
        $sortField = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : "created_at";
        $sortDir = $this->sortDirection === "asc" ? "asc" : "desc";

        if ($this->ordersTableReady()) {
            $orders = Order::query()
                ->with([
                    "user:id,name,email",
                    "items.item:id,name",
                ])
                ->withCount("items")
                ->when($this->search !== "", function ($q) {
                    $search = $this->search;
                    $q->where(function ($qq) use ($search) {
                        $qq->where("status", "like", "%{$search}%")
                            ->orWhere("id", "like", "%{$search}%")
                            ->orWhereHas("user", fn($uq) => $uq
                                ->where("name", "like", "%{$search}%")
                                ->orWhere("email", "like", "%{$search}%"));
                    });
                })
                ->orderBy($sortField, $sortDir)
                ->paginate(15)
                ->withQueryString();
        } else {
            $this->setupError = "Tabela de pedidos não encontrada. Rode as migrations.";
            $orders = new LengthAwarePaginator([], 0, 15, $this->getPage());
        }

        $statusOptions = [
            ["id" => "pending", "name" => "Pending"],
            ["id" => "paid", "name" => "Paid"],
            ["id" => "canceled", "name" => "Canceled"],
        ];

        $itemsOrder = null;
        if ($this->itemsId) {
            $itemsOrder = Order::with(["items.item:id,name"])->find($this->itemsId);
        }

        return view("livewire.admin.orders.index", [
            "orders" => $orders,
            "statusOptions" => $statusOptions,
            "itemsOrder" => $itemsOrder,
        ])->layout("layouts.admin.app");
    }

    private function ordersTableReady(): bool
    {
        return Schema::hasTable("orders");
    }
}
