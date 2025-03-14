<?php

namespace App\View\Components;

use App\Sources\Source;
use Illuminate\View\Component;

class SourceIcon extends Component
{
    protected ?Source $source;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $type)
    {
        $this->source = source($type);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if ($this->source instanceof \App\Sources\Source) {
            return $this->source->getIcon();
        }
        return view('components.source-icon');
    }
}
