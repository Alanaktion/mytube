<button type="button"
    class="p-2 rounded-full text-sm font-medium focus:outline-none text-red-600 focus:bg-gray-300 dark:focus:bg-trueGray-700 dark:text-red-500 hover:bg-gray-300 dark:hover:bg-trueGray-700 tooltip-right sm:tooltip-center"
    data-tooltip
    x-data='@json(['isFavorite' => $isFavorite(), 'uuid' => $video->uuid])'
    x-bind:aria-label="isFavorite ? '{{ __('Remove from Favorites') }}' : '{{ __('Add to Favorites') }}'"
    x-init="$watch('isFavorite', val => setIsFavorite(val))"
    @click="isFavorite = !isFavorite">
    <svg class="w-6 h-6" x-bind:fill="isFavorite ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
</button>

<script>
function setIsFavorite(val) {
    const csrfToken = document.querySelector('[name="csrf-token"]').getAttribute('content');
    fetch('/favorites/toggleVideo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken,
        },
        body: JSON.stringify({
            uuid: @json($video->uuid),
            value: val,
        }),
    });
}
</script>
