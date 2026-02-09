<?php

namespace App\Livewire\Admin\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Index extends Component
{
    public array $kpis = [];
    public array $trendLabels = [];
    public array $trend = [];
    public array $subscriptionStatus = [];
    public array $paymentStatus = [];
    public array $topPlans = [];
    public ?string $billingWarning = null;

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    private function tableOrNull(array $candidates): ?string
    {
        foreach ($candidates as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return null;
    }

    private function countByDate(string $table, Carbon $date): int
    {
        if (!Schema::hasColumn($table, "created_at")) {
            return 0;
        }

        return (int) DB::table($table)
            ->whereDate("created_at", $date->toDateString())
            ->count();
    }

    private function loadDashboardData(): void
    {
        $usersTable = $this->tableOrNull(["users"]);
        $collectionsTable = $this->tableOrNull(["collections"]);
        $itemsTable = $this->tableOrNull(["items"]);
        $categoriesTable = $this->tableOrNull(["categories"]);
        $tagsTable = $this->tableOrNull(["tags"]);
        $downloadsTable = $this->tableOrNull(["downloads"]);
        $plansTable = $this->tableOrNull(["plans", "plan"]);
        $subscriptionsTable = $this->tableOrNull(["subscriptions", "subscription"]);
        $paymentsTable = $this->tableOrNull(["payments", "payment"]);

        $this->kpis = [
            "users" => $usersTable ? (int) DB::table($usersTable)->count() : 0,
            "collections" => $collectionsTable ? (int) DB::table($collectionsTable)->count() : 0,
            "items" => $itemsTable ? (int) DB::table($itemsTable)->count() : 0,
            "downloads" => $downloadsTable ? (int) DB::table($downloadsTable)->count() : 0,
            "plans" => $plansTable ? (int) DB::table($plansTable)->count() : 0,
            "revenue_approved" => $paymentsTable
                ? (float) DB::table($paymentsTable)
                    ->where("status", "approved")
                    ->sum("amount")
                : 0,
            "categories" => $categoriesTable ? (int) DB::table($categoriesTable)->count() : 0,
            "tags" => $tagsTable ? (int) DB::table($tagsTable)->count() : 0,
            "subscriptions" => $subscriptionsTable ? (int) DB::table($subscriptionsTable)->count() : 0,
            "payments" => $paymentsTable ? (int) DB::table($paymentsTable)->count() : 0,
        ];

        $days = collect(range(6, 0))
            ->map(fn($i) => now()->copy()->subDays($i))
            ->values();

        $this->trendLabels = $days
            ->map(fn($d) => $d->format("d/m"))
            ->all();

        $this->trend = [
            "users" => $usersTable
                ? $days->map(fn($d) => $this->countByDate($usersTable, $d))->all()
                : array_fill(0, 7, 0),
            "collections" => $collectionsTable
                ? $days->map(fn($d) => $this->countByDate($collectionsTable, $d))->all()
                : array_fill(0, 7, 0),
            "downloads" => $downloadsTable
                ? $days->map(fn($d) => $this->countByDate($downloadsTable, $d))->all()
                : array_fill(0, 7, 0),
            "payments" => $paymentsTable
                ? $days->map(fn($d) => $this->countByDate($paymentsTable, $d))->all()
                : array_fill(0, 7, 0),
        ];

        $this->subscriptionStatus = $subscriptionsTable
            ? DB::table($subscriptionsTable)
                ->select("status", DB::raw("COUNT(*) as total"))
                ->groupBy("status")
                ->orderByDesc("total")
                ->get()
                ->map(fn($r) => ["label" => (string) $r->status, "value" => (int) $r->total])
                ->values()
                ->all()
            : [];

        $this->paymentStatus = $paymentsTable
            ? DB::table($paymentsTable)
                ->select("status", DB::raw("COUNT(*) as total"))
                ->groupBy("status")
                ->orderByDesc("total")
                ->get()
                ->map(fn($r) => ["label" => (string) $r->status, "value" => (int) $r->total])
                ->values()
                ->all()
            : [];

        $this->topPlans = [];
        if ($plansTable && $subscriptionsTable) {
            $this->topPlans = DB::table($plansTable . " as p")
                ->leftJoin($subscriptionsTable . " as s", "s.plan_id", "=", "p.id")
                ->select(
                    "p.id",
                    "p.name",
                    "p.price",
                    DB::raw("COUNT(s.id) as subscriptions_count"),
                    DB::raw("SUM(CASE WHEN s.status = 'active' THEN 1 ELSE 0 END) as active_count"),
                )
                ->groupBy("p.id", "p.name", "p.price")
                ->orderByDesc("active_count")
                ->orderByDesc("subscriptions_count")
                ->limit(6)
                ->get()
                ->map(fn($r) => [
                    "id" => (int) $r->id,
                    "name" => (string) $r->name,
                    "price" => (float) ($r->price ?? 0),
                    "subscriptions_count" => (int) ($r->subscriptions_count ?? 0),
                    "active_count" => (int) ($r->active_count ?? 0),
                ])
                ->all();
        }

        $missing = [];
        if (!$plansTable) {
            $missing[] = "plans";
        }
        if (!$subscriptionsTable) {
            $missing[] = "subscriptions";
        }
        if (!$paymentsTable) {
            $missing[] = "payments";
        }

        $this->billingWarning = !empty($missing)
            ? "Tabelas de billing ausentes: " . implode(", ", $missing) . ". Rode as migrations para ver dados completos."
            : null;
    }

    public function render()
    {
        return view("livewire.admin.dashboard.index")->layout("layouts.admin.app");
    }
}
