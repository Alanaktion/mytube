<?php

namespace App\View\Components;

use App\Sources\Source;
use Illuminate\View\Component;

class FilterDialog extends Component
{
    /** @var array<string, string> */
    public array $sources;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        /** @var Source[] */
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
        return view('components.filter-dialog');
    }
}
