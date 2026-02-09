<?php

namespace App\Livewire\App;

use Carbon\Carbon;
use Livewire\Component;

class Billing extends Component
{
    public $subscription;
    public $downloadsHoje;
    public $limiteDiario;
    public $percentualDownloads;
    public $inicio;
    public $vencimento;
    public $faturas;

    public function mount()
    {
        $user = auth()->user();

        $this->loadSubscription($user);
        $this->loadDownloads($user);
        $this->calculateProgress();
        $this->loadDates();
        $this->loadFaturas($user);
    }

    /**
     * Carrega a assinatura do usuário com o plano.
     */
    private function loadSubscription($user): void
    {
        $this->subscription = $user->subscriptions()->with("plan")->first();

        $this->limiteDiario = $this->subscription?->plan?->limit_download ?? 0;
    }

    /**
     * Conta quantos downloads o usuário fez no dia.
     */
    private function loadDownloads($user): void
    {
        $hoje = Carbon::today();

        $this->downloadsHoje = $user
            ->downloads()
            ->whereDate("created_at", $hoje)
            ->count();
    }

    /**
     * Calcula o percentual da barra de progresso.
     */
    private function calculateProgress(): void
    {
        $this->percentualDownloads =
            $this->limiteDiario > 0
                ? ($this->downloadsHoje / $this->limiteDiario) * 100
                : 0;
    }

    /**
     * Define as datas formatadas de início e vencimento.
     */
    private function loadDates(): void
    {
        if ($this->subscription) {
            $this->inicio = $this->subscription->started_at?->format("d/m/Y");
            $this->vencimento = $this->subscription->expires_at?->format(
                "d/m/Y",
            );
        }
    }

    /**
     * Carrega as faturas/pagamentos da assinatura (ou um collection vazio).
     */
    private function loadFaturas($user): void
    {
        if ($this->subscription) {
            $this->faturas = $this->subscription
                ->payments()
                ->with("subscription.plan") // importante!
                ->latest()
                ->take(5)
                ->get();
        } else {
            $this->faturas = collect();
        }
    }

    public function render()
    {
        return view("livewire.app.billing")->layout("layouts.app");
    }
}
