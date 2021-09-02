<?php

namespace App\View\Components;

use App\Models\Channel;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class FavoriteToggle extends Component
{
    public string $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public Video|Playlist|Channel $model)
    {
        $this->type = strtolower(class_basename($model));
    }

    public function isFavorite(): bool
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany $relation */
        $relation = match ($this->type) {
            'video' => $user->favoriteVideos(),
            'playlist' => $user->favoritePlaylists(),
            'channel' => $user->favoriteChannels(),
            default => throw new \Exception('Unknown favorite type: ' . $this->type),
        };
        return $relation
            ->where("{$this->type}_id", $this->model->id)
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
