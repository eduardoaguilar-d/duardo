<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppShell extends Component
{
    public function __construct(
        public string $drawerId = 'drawer-navigation'
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.app-shell');
    }
}
