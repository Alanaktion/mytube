<div id="app-favorite-toggle">
    <favorite-toggle
        uuid="{{ $model->uuid }}"
        type="{{ $type }}"
        {{ $isFavorite() ? 'is-favorite' : '' }}
    ></favorite-toggle>
</div>
