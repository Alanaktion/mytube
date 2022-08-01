<filter-dialog
    :sources='@json($sources)'
    :params='@json($_GET)'
>
    @foreach (array_keys($sources) as $key)
        <template v-slot:icon-{{ $key }}>
            <x-source-icon :type="$key" class="h-5 w-5 mr-2" />
        </template>
    @endforeach
</filter-dialog>
