<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public bool $showHeaderLogo;

    public function __construct(bool $showHeaderLogo = true)
    {
        $this->showHeaderLogo = $showHeaderLogo;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
