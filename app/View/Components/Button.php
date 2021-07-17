<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
    public $primary;
    public $rounded;
    public $small;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(bool $primary = false, bool $rounded = false, bool $small = false)
    {
        $this->primary = $primary;
        $this->rounded = $rounded;
        $this->small = $small;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button');
    }
}
