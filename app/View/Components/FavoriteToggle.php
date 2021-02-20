<?php

namespace App\View\Components;

use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class FavoriteToggle extends Component
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

    public function isFavorite(): bool
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        return $user->favoriteVideos()
            ->where('video_id', $this->video->id)
            ->exists();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.favorite-toggle');
    }
}
