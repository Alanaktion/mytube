<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SourceIcon extends Component
{
    protected $source;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $type)
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            if ($source->getSourceType() == $type) {
                $this->source = $source;
                return;
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if ($this->source) {
            return $this->source->getIcon();
        }
        return view('components.source-icon');
    }
}
