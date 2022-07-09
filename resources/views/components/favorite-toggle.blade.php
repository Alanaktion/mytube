<div id="app-favorite-toggle">
    <favorite-toggle
        uuid="{{ $model->uuid }}"
        type="{{ $type }}"
        :is-favorite="@json($isFavorite())"
    ></favorite-toggle>
</div>
