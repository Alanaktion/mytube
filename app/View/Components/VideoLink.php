<?php

namespace App\View\Components;

use App\Models\Video;
use Illuminate\View\Component;

class VideoLink extends Component
{
    public $video;
    public $showChannel;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Video $video, bool $showChannel = false)
    {
        $this->video = $video;
        $this->showChannel = $showChannel;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.video-link');
    }
}
