<favorite-toggle
    uuid="{{ $model->uuid }}"
    type="{{ $type }}"
    :is-favorite="@json($isFavorite())"
></favorite-toggle>
