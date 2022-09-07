<button class="btn btn-secondary rounded-full" on:click={() => (open = true)}>
    {$t('Refresh')}
</button>

<AppDialog title={$t('Refresh channel')} open={open} on:close={() => (open = false)}>
    <form action={`/channels/${uuid}`} method="post">
        <input type="hidden" name="_token" value={csrfToken}>
        <input type="hidden" name="_method" value="PUT">

        <p class="text-sm text-slate-500 dark:text-neutral-400 mb-6">
            This will import any new content from the source.
        </p>

        <div class="flex items-start mb-3">
            <div class="flex items-center h-5">
                <input type="checkbox" class="input-checkbox" id="playlists" name="playlists" bind:checked={playlists} value="1">
            </div>
            <div class="ml-3 text-sm">
                <label for="playlists" class="font-semibold text-slate-700 dark:text-neutral-300">
                    {$t('Playlists')}
                </label>
                <p class="text-slate-500 dark:text-neutral-400">
                    Import all playlists on the channel with their corresponding videos.
                </p>
            </div>
        </div>
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input type="checkbox" class="input-checkbox" id="videos" name="videos" bind:checked={videos} value="1">
            </div>
            <div class="ml-3 text-sm leading-5">
                <label for="videos" class="font-semibold text-slate-700 dark:text-neutral-300">
                    {$t('Videos')}
                </label>
                <p class="text-slate-500 dark:text-neutral-400">
                    Import all videos on the channel.
                </p>
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="btn btn-primary">
                Refresh
            </button>
        </div>
    </form>
</AppDialog>

<script>
import AppDialog from './AppDialog.svelte';
import { t } from 'svelte-i18n';

export let uuid = '';

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let open = false;
let playlists = true;
let videos = true;
</script>
