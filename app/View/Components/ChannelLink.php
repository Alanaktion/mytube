<?php

namespace App\View\Components;

use App\Models\Channel;
use Illuminate\View\Component;

class ChannelLink extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public Channel $channel)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.channel-link');
    }
}
