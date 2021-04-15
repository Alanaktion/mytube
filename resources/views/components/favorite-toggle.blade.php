<favorite-toggle
    uuid="{{ $video->uuid }}"
    :is-favorite="@json($isFavorite())"
    add-label="{{ __('Add to Favorites') }}"
    remove-label="{{ __('Remove from Favorites') }}"
></favorite-toggle>
