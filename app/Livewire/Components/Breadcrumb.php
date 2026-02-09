<?php
namespace App\Livewire\Components;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class Breadcrumb extends Component
{
    public array $items = [];

    public function mount()
    {
        $segments = Request::segments(); // ['pt-BR', 'dashboard', 'admin', 'product']

        // Remove o primeiro segmento se for uma localização suportada
        $supportedLocales = array_keys(
            LaravelLocalization::getSupportedLocales(),
        );

        if (!empty($segments) && in_array($segments[0], $supportedLocales)) {
            array_shift($segments); // remove 'pt-BR'
        }
        $url = "";
        $segments = Request::segments();
        $supportedLocales = array_keys(
            LaravelLocalization::getSupportedLocales(),
        );

        if (!empty($segments) && in_array($segments[0], $supportedLocales)) {
            array_shift($segments); // remove o locale
        }

        // Só adiciona "Dashboard" se o primeiro segmento não for "dashboard"
        if (!empty($segments) && $segments[0] !== "home") {
            $this->items[] = [
                "label" => __("Dashboard"),
                "url" => route("home"),
            ];
        }

        $url = "";
        foreach ($segments as $segment) {
            $label = $this->translate($segment);
            if (!$label) {
                continue;
            } // ignora segmentos mapeados como null

            $url .= "/" . $segment;
            $localizedUrl = LaravelLocalization::getLocalizedURL(
                app()->getLocale(),
                $url,
            );

            $this->items[] = [
                "label" => $label,
                "url" => $localizedUrl,
            ];
        }
        if (!empty($this->items)) {
            $this->items[array_key_last($this->items)]["url"] = null;
        }
    }

    private function translate($key)
    {
        // Segments que você quer substituir por algo ou ignorar
        $map = [
            "v" => null, // 'v' some do breadcrumb
            "c" => "Categorias",
            // outros se precisar
        ];

        if (array_key_exists($key, $map)) {
            return $map[$key]; // se null, vai ser ignorado
        }

        $key = str_replace(["-", "_"], " ", $key);
        return ucwords($key);
    }

    public function render()
    {
        return view("livewire.components.breadcrumb");
    }
}
