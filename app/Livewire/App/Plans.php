<?php

namespace App\Livewire\App;

use Livewire\Component;
use App\Models\Plan;

class Plans extends Component
{
    public function render()
    {
        $query = Plan::query();

        $features = [];

        // filtro por features
        if (!empty($features)) {
            foreach ($features as $feature) {
                $query->whereJsonContains("features", $feature);
            }
        }

        $plans = $query->get();
        return view("livewire.app.plans", [
            "plans" => $plans,
            "features" => $features,
        ])->layout("layouts.app");
    }
}
