<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FilterSource extends Component
{
    public $sources;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public ?string $value)
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            $this->sources[$source->getSourceType()] = $source->getDisplayName();
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.filter-source');
    }
}
