<?php

declare(strict_types=1);

namespace App\Livewire\Navigation;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class NavigationMenu extends Component
{
    public function render(): View
    {
        return view('livewire.navigation.navigation-menu');
    }
}
