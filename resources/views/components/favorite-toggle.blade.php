<div id="app-favorite-toggle">
    <favorite-toggle
        uuid="{{ $model->uuid }}"
        type="{{ $type }}"
        {{ $attributes }}
        :is-favorite="@json($isFavorite())"
    ></favorite-toggle>
</div>
