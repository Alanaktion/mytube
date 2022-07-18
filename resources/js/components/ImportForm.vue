<template>
    <TabGroup @change="changeTab">
        <form :action="action" method="post" class="shadow dark:shadow-inner-white-top overflow-hidden sm:rounded-md bg-white dark:bg-neutral-800">
            <input type="hidden" name="_token" :value="csrfToken">
            <div class="flex justify-between items-center px-4 sm:px-6 py-3">
                <div class="text-sm uppercase font-semibold text-slate-500 dark:text-neutral-400">
                    {{ $t('Import') }}
                </div>
                <TabList class="flex gap-2">
                    <Tab v-slot="{ selected }" as="template">
                        <button :class="{
                            'px-3 py-2 rounded-md': true,
                            'text-primary-700 bg-primary-100 dark:text-white dark:bg-primary-700': selected,
                            'text-slate-700 hover:bg-slate-50 dark:text-neutral-300 dark:hover:bg-neutral-700': !selected
                        }">
                            {{ $t('Video') }}
                        </button>
                    </Tab>
                    <Tab v-slot="{ selected }" as="template">
                        <button :class="{
                            'px-3 py-2 rounded-md': true,
                            'text-primary-700 bg-primary-100 dark:text-white dark:bg-primary-700': selected,
                            'text-slate-700 hover:bg-slate-50 dark:text-neutral-300 dark:hover:bg-neutral-700': !selected
                        }">
                            {{ $t('Playlist') }}
                        </button>
                    </Tab>
                    <Tab v-slot="{ selected }" as="template">
                        <button :class="{
                            'px-3 py-2 rounded-md': true,
                            'text-primary-700 bg-primary-100 dark:text-white dark:bg-primary-700': selected,
                            'text-slate-700 hover:bg-slate-50 dark:text-neutral-300 dark:hover:bg-neutral-700': !selected
                        }">
                            {{ $t('Channel') }}
                        </button>
                    </Tab>
                </TabList>
            </div>
            <div class="px-4 pb-5 sm:px-6 sm:pb-6">
                <TabPanels>
                    <TabPanel>
                        <label for="videoIds" class="form-label mb-1">
                            {{ $t('Video IDs/URLs') }}
                        </label>
                        <textarea name="videoIds" id="videoIds" v-model="videoIds" class="input-text" rows="5" required />
                    </TabPanel>
                    <TabPanel>
                        <label for="playlistIds" class="form-label mb-1">
                            {{ $t('Playlist URLs/IDs') }}
                        </label>
                        <textarea name="playlistIds" id="playlistIds" v-model="playlistIds" class="input-text" rows="5" required />
                    </TabPanel>
                    <TabPanel>
                        <label for="channelId" class="form-label mb-1">
                            {{ $t('Channel URL') }}
                        </label>
                        <input class="input-text mb-4 lg:mb-6" type="text" name="channelId" id="channelId" v-model="channelId" required>

                        <div class="flex items-start mb-3">
                            <div class="flex items-center h-5">
                                <input type="checkbox" class="input-checkbox" id="playlists" name="playlists" v-model="playlists">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="playlists" class="font-semibold text-slate-700 dark:text-neutral-300">
                                    {{ $t('Playlists') }}
                                </label>
                                <p class="text-slate-500 dark:text-neutral-400">
                                    Import all playlists on the channel with their corresponding videos. (Supported sources only)
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" class="input-checkbox" id="videos" name="videos" v-model="videos">
                            </div>
                            <div class="ml-3 text-sm leading-5">
                                <label for="videos" class="font-semibold text-slate-700 dark:text-neutral-300">
                                    {{ $t('Videos') }}
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
                    {{ $t('Start import') }}
                </button>
            </div>
        </form>
    </TabGroup>
</template>

<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import { ref } from 'vue';

defineProps({
    csrfToken: {
        type: String,
        required: true,
    },
});

const actions = [
    '/admin/videos',
    '/admin/playlists',
    '/admin/channels',
];

const videoIds = ref('');
const playlistIds = ref('');
const channelId = ref('');
const playlists = ref(false);
const videos = ref(false);

const action = ref(actions[0]);
function changeTab(index) {
    action.value = actions[index]
}
</script>
