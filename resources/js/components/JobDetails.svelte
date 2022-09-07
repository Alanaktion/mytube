{#each details as item}
<div class="px-3 py-2 mb-4 flex items-center gap-2 bg-primary-100 dark:bg-primary-900 bg-opacity-50 border border-primary-100 dark:border-primary-600 p-3 rounded">
    <svg class="animate-spin h-5 w-5 text-primary-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span class="whitespace-nowrap shrink-0">
        {typeDisplay(item)}&hellip;
    </span>
    {#if item.model}
        <a class="text-primary-700 dark:text-primary-300 cursor-pointer truncate" href={modelHref(item)}>
            {item.model.title}
        </a>
    {/if}
    {#if item.data?.count}
    <div
        class="relative flex items-stretch bg-white dark:bg-neutral-900 rounded overflow-hidden w-32 h-5 ml-auto shadow-sm shrink-0"
        role="progressbar"
        aria-valuemax={item.data.count}
        aria-valuenow={item.data.imported}
    >
        <div class="bg-primary-300 dark:bg-primary-700" style:width={`${item.data.imported / item.data.count * 100}%`} />
        <div class="absolute top-1 right-2 leading-none text-xs font-bold">
            {item.data.imported}/{item.data.count}
        </div>
    </div>
    {/if}
</div>
{/each}

<script>
export let type;
export let id;

let details = [];
const loadDetails = () => {
    const params = [];
    if (type) {
        params.push(`type=${encodeURIComponent(type)}`);
    }
    if (id) {
        params.push(`id=${encodeURIComponent(id)}`);
    }
    fetch(`/job-details?${params.join('&')}`)
        .then(response => response.json())
        .then(data => {
            details = data;
        });
};
loadDetails();

const typeDisplay = (item => {
    switch (item.type) {
        case 'import_items':
            return 'Importing playlist items';
        case 'download_video':
            return 'Downloading video';
        default:
            return type;
    }
});
const modelHref = (item => {
    switch (item.model_type) {
        case 'App\\Models\\Playlist':
            return `/playlists/${item.model.uuid}`;
        case 'App\\Models\\Video':
            return `/videos/${item.model.uuid}`;
        case 'App\\Models\\Channel':
            return `/channels/${item.model.uuid}`;
    }
});

setInterval(loadDetails, 2500);
</script>
