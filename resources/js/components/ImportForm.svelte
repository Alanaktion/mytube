<TabGroup on:change={changeTab}>
    <form action={action} method="post" class="shadow dark:shadow-inner-white-top overflow-hidden sm:rounded-md bg-white dark:bg-neutral-800">
        <input type="hidden" name="_token" value={csrfToken}>
        <div class="flex justify-between items-center px-4 sm:px-6 py-3">
            <div class="text-sm uppercase font-semibold text-slate-500 dark:text-neutral-400">
                {$t('Import')}
            </div>
            <TabList class="flex gap-2">
                <Tab class={({ selected }) => (`px-3 py-2 rounded-md ${selected ? 'text-primary-700 bg-primary-100 dark:text-white dark:bg-primary-700' : 'text-slate-700 hover:bg-slate-50 dark:text-neutral-300 dark:hover:bg-neutral-700'}`)}>
                    {$t('Video')}
                </Tab>
                <Tab class={({ selected }) => (`px-3 py-2 rounded-md ${selected ? 'text-primary-700 bg-primary-100 dark:text-white dark:bg-primary-700' : 'text-slate-700 hover:bg-slate-50 dark:text-neutral-300 dark:hover:bg-neutral-700'}`)}>
                    {$t('Playlist')}
                </Tab>
                <Tab class={({ selected }) => (`px-3 py-2 rounded-md ${selected ? 'text-primary-700 bg-primary-100 dark:text-white dark:bg-primary-700' : 'text-slate-700 hover:bg-slate-50 dark:text-neutral-300 dark:hover:bg-neutral-700'}`)}>
                    {$t('Channel')}
                </Tab>
            </TabList>
        </div>
        <div class="px-4 pb-5 sm:px-6 sm:pb-6">
            <TabPanels>
                <TabPanel>
                    <label for="videoIds" class="form-label mb-1">
                        {$t('Video IDs/URLs')}
                    </label>
                    <textarea name="videoIds" id="videoIds" bind:value={videoIds} class="input-text" rows="5" required />
                </TabPanel>
                <TabPanel>
                    <label for="playlistIds" class="form-label mb-1">
                        {$t('Playlist URLs/IDs')}
                    </label>
                    <textarea name="playlistIds" id="playlistIds" bind:value={playlistIds} class="input-text" rows="5" required />
                </TabPanel>
                <TabPanel>
                    <label for="channelId" class="form-label mb-1">
                        {$t('Channel URL')}
                    </label>
                    <input class="input-text mb-4 lg:mb-6" type="text" name="channelId" id="channelId" bind:value={channelId} required>

                    <div class="flex items-start mb-3">
                        <div class="flex items-center h-5">
                            <input type="checkbox" class="input-checkbox" id="playlists" name="playlists" bind:checked={playlists} value="1">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="playlists" class="font-semibold text-slate-700 dark:text-neutral-300">
                                {$t('Playlists')}
                            </label>
                            <p class="text-slate-500 dark:text-neutral-400">
                                Import all playlists on the channel with their corresponding videos. (Supported sources only)
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
                                Import all videos on the channel. (Supported sources only)
                            </p>
                        </div>
                    </div>
                </TabPanel>
            </TabPanels>
        </div>
        <div class="px-4 py-3 bg-slate-100 dark:bg-neutral-850 shadow-inner text-right sm:px-6">
            <button type="submit" class="btn btn-primary">
                {$t('Start import')}
            </button>
        </div>
    </form>
</TabGroup>

<script>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@rgossiaux/svelte-headlessui';
import { t } from 'svelte-i18n';

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const actions = [
    '/admin/videos',
    '/admin/playlists',
    '/admin/channels',
];

let videoIds = '';
let playlistIds = '';
let channelId = '';
let playlists = false;
let videos = false;

let action = actions[0];
function changeTab(index) {
    action = actions[index]
}
</script>
