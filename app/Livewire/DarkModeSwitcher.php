<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DarkModeSwitcher extends Component
{
    public function render(): View
    {
        return view('components.dark-mode-switcher');
    }
}
