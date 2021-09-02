<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class NavLink extends Component
{
    public bool $active;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $href, public string $text)
    {
        $this->active = Route::current()->uri === ltrim($href, '/');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.nav-link');
    }
}
