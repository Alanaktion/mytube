<?php

namespace App\View\Components;

use App\Models\Video;
use Illuminate\View\Component;

class VideoLink extends Component
{
    public $video;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
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
