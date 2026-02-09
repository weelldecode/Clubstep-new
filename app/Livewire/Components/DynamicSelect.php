<?php

namespace App\Livewire\Components;

use Livewire\Component;

class DynamicSelect extends Component
{
    public $options = []; // Array de opções
    public $selected = null; // Valor selecionado
    public string $placeholder = "Selecione uma opção";
    public string $label = ""; // Label opcional
    public $emitOnChange = null; // Evento Livewire emitido quando muda

    public function mount(
        $options = [],
        $selected = null,
        $placeholder = null,
        $label = null,
        $emitOnChange = null,
    ) {
        $this->options = $options;
        $this->selected = $selected;
        if ($placeholder) {
            $this->placeholder = $placeholder;
        }
        if ($label) {
            $this->label = $label;
        }
        $this->emitOnChange = $emitOnChange;
    }

    public function updatedSelected()
    {
        if ($this->emitOnChange) {
            $this->emit($this->emitOnChange, $this->selected);
        }
    }

    public function render()
    {
        return view("livewire.components.dynamic-select");
    }
}
