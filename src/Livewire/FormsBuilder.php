<?php

declare(strict_types=1);

namespace Art35rennes\DaisyKit\Livewire;

use Livewire\Component;

class FormsBuilder extends Component
{
    public string $schema = '{"fields":[]}';

    public function render(): string
    {
        return '<div data-daisy-kit-livewire-builder></div>';
    }
}
