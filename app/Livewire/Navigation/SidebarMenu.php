<?php

declare(strict_types=1);

namespace App\Livewire\Navigation;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class SidebarMenu extends Component
{
    public function render(): View
    {
        return view('livewire.navigation.sidebar-menu');
    }
}
