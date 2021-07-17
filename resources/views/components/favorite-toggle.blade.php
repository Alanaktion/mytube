<favorite-toggle
    uuid="{{ $model->uuid }}"
    type="{{ $type }}"
    :is-favorite="@json($isFavorite())"
    add-label="{{ __('Add to Favorites') }}"
    remove-label="{{ __('Remove from Favorites') }}"
></favorite-toggle>
