<?php

namespace App\Livewire\App\Collection;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

use App\Models\Collection;
use App\Models\Item;
use App\Models\Report;
use App\Models\Cart;

use App\Domain\Taxonomy\Queries\TaxonomyListsQuery;
use App\Domain\Collections\Queries\CollectionItemsQuery;
use App\Domain\Collections\Queries\RelatedCollectionsQuery;
use App\Domain\Downloads\Actions\StartCollectionDownload;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class ViewCollection extends Component
{
    use WithPagination, AuthorizesRequests;

    public Collection $collection;

    public array $selectedItemCategories = [];

    public string $search = "";
    public string $sortField = "name";
    public string $sortDirection = "asc";
    public string $viewMode = "card";
    public array $previewImages = [];
    public $allItemCategories = [];

    public ?Item $selectedItem = null;
    public bool $showModal = false;
    public bool $showReportModal = false;
    public ?int $reportItemId = null;
    public array $reportForm = [
        "reason" => "",
        "message" => "",
    ];

    protected $queryString = [
        "search" => ["except" => ""],
        "selectedItemCategories" => ["except" => []],
        "sortField" => ["except" => "name"],
        "sortDirection" => ["except" => "asc"],
        "page" => ["except" => 1],
    ];

    public function mount(Collection $collection): void
    {
        $this->collection = $collection;
        //  $this->authorize("view", $collection); // funciona se o componente usar AuthorizesRequests
    }

    public function updated($name): void
    {
        if (
            $name === "search" ||
            str_starts_with($name, "selected") ||
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

    public function showItem(int $id): void
    {
        $this->selectedItem = Item::findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedItem = null;
    }

    public function openReport(int $itemId): void
    {
        if (!Auth::check()) {
            $this->dispatch("notify", message: "Faça login para denunciar.");
            return;
        }

        $this->reportItemId = $itemId;
        $this->reportForm = [
            "reason" => "",
            "message" => "",
        ];
        $this->showReportModal = true;
    }

    public function submitReport(): void
    {
        if (!Auth::check()) {
            $this->dispatch("notify", message: "Faça login para denunciar.");
            return;
        }

        $this->validate([
            "reportForm.reason" => ["required", "string", "max:100"],
            "reportForm.message" => ["nullable", "string", "max:2000"],
        ]);

        if (!$this->reportItemId) {
            return;
        }

        Report::create([
            "user_id" => Auth::id(),
            "item_id" => $this->reportItemId,
            "reason" => $this->reportForm["reason"],
            "message" => $this->reportForm["message"] ?: null,
            "status" => "open",
        ]);

        $this->showReportModal = false;
        $this->dispatch("notify", message: "Denúncia enviada.");
    }

    public function closeReport(): void
    {
        $this->showReportModal = false;
    }

    public function startDownload(StartCollectionDownload $action, ?int $itemId = null): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->dispatch(
                "notify",
                message: "Faça login para baixar coleções.",
            );
            return;
        }

        if ($itemId) {
            $item = Item::find($itemId);
            if ($item && $item->type === "sites") {
                $this->dispatch(
                    "notify",
                    message: "Itens do tipo sites devem ser comprados.",
                );
                return;
            }
        }

        $result = $action->run($user, $this->collection);

        $this->dispatch("notify", message: $result["message"]);
    }

    public function addToCart(int $itemId): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->dispatch("notify", message: "Faça login para adicionar ao carrinho.");
            return;
        }

        $item = Item::findOrFail($itemId);

        if ($item->type !== "sites") {
            $this->dispatch("notify", message: "Este item não pode ser comprado avulso.");
            return;
        }

        $cart = Cart::firstOrCreate([
            "user_id" => $user->id,
            "status" => "active",
        ]);

        $hasOtherItems = $cart->items()->where("item_id", "!=", $item->id)->exists();
        if ($hasOtherItems) {
            $this->dispatch("notify", message: "Seu carrinho já tem um item. Finalize a compra antes de adicionar outro.");
            return;
        }

        $existing = $cart->items()->where("item_id", $item->id)->first();

        if ($existing) {
            $existing->update([
                "quantity" => 1,
            ]);
        } else {
            $cart->items()->create([
                "item_id" => $item->id,
                "price" => $item->price,
                "quantity" => 1,
            ]);
        }

        $this->dispatch("notify", message: "Item adicionado ao carrinho.");
        $this->dispatch("cart-updated");
    }

    public function toggleFavorite(int $itemId): void
    {
        $user = Auth::user();

        if (!$user) {
            $this->dispatch("notify", message: "Faça login para salvar itens.");
            return;
        }

        $item = Item::findOrFail($itemId);

        $exists = $user->favorites()->where("item_id", $item->id)->exists();

        if ($exists) {
            $user->favorites()->detach($item->id);
            $this->dispatch("notify", message: "Item removido dos favoritos.");
        } else {
            $user->favorites()->attach($item->id);
            $this->dispatch("notify", message: "Item salvo nos favoritos.");
        }

        $this->dispatch("wishlist-updated");
    }

    public function isFavorited(int $itemId): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->favorites()->where("item_id", $itemId)->exists();
    }

    public function hasPurchasedItem(int $itemId): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->orders()
            ->where("status", "paid")
            ->whereHas("items", fn($q) => $q->where("item_id", $itemId))
            ->exists();
    }

    public function hasActiveCartItem(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->carts()
            ->where("status", "active")
            ->whereHas("items")
            ->exists();
    }

    public function isItemInCart(int $itemId): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->carts()
            ->where("status", "active")
            ->whereHas("items", fn($q) => $q->where("item_id", $itemId))
            ->exists();
    }

    public function render(
        TaxonomyListsQuery $tax,
        CollectionItemsQuery $itemsQuery,
        RelatedCollectionsQuery $relatedQuery,
    ) {
        // carrega taxonomia (cacheada no Domain)
        $this->allItemCategories = $tax->itemsCategories();

        // carrega collection com relações necessárias (ajuste author/user conforme seu model)
        $collection = $this->collection->load(["categories", "author"]);

        $itemsForPreview = $collection
            ->items()
            ->select(["id", "images", "image_path"]) // ajuste conforme seu schema
            ->latest()
            ->limit(12)
            ->get();

        $images = [];

        foreach ($itemsForPreview as $it) {
            // 1) se tiver image_path simples
            if (!empty($it->image_path)) {
                $images[] = $it->image_path;
                if (count($images) === 4) {
                    break;
                }
            }

            // 2) se tiver campo images (json/array)
            if (!empty($it->images)) {
                $imgs = is_array($it->images)
                    ? $it->images
                    : json_decode($it->images, true);
                if (is_array($imgs)) {
                    foreach ($imgs as $img) {
                        if ($img) {
                            $images[] = $img;
                            if (count($images) === 4) {
                                break 2;
                            }
                        }
                    }
                }
            }
        }

        $this->previewImages = $images;
        $items = $itemsQuery
            ->build(
                $collection,
                $this->search,
                $this->selectedItemCategories,
                $this->sortField,
                $this->sortDirection,
            )
            ->paginate(9);

        $relatedCollections = $relatedQuery->run($collection, 4);

        return view("livewire.app.collections.view-collection", [
            "collection" => $collection,
            "items" => $items,
            "relatedCollections" => $relatedCollections,
        ])
            ->title($collection->name)
            ->layout("layouts.app");
    }
}
