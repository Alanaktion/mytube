<template>
    <TabGroup @change="changeTab">
        <form :action="action" method="post" class="shadow dark:shadow-inner-white-top overflow-hidden sm:rounded-md bg-white dark:bg-trueGray-800">
            <slot></slot>
            <div class="flex justify-between items-center px-4 sm:px-6 py-3">
                <div class="text-sm uppercase font-semibold text-gray-500 dark:text-trueGray-400">
                    {{ $t('Import') }}
                </div>
                <TabList class="flex gap-2">
                    <Tab v-slot="{ selected }" as="template">
                        <button :class="{
                            'px-3 py-2 rounded-md': true,
                            'text-blue-700 bg-blue-100 dark:text-white dark:bg-blue-700': selected,
                            'text-gray-700 hover:bg-gray-50 dark:text-trueGray-300 dark:hover:bg-trueGray-700': !selected
                        }">
                            {{ $t('Video') }}
                        </button>
                    </Tab>
                    <Tab v-slot="{ selected }" as="template">
                        <button :class="{
                            'px-3 py-2 rounded-md': true,
                            'text-blue-700 bg-blue-100 dark:text-white dark:bg-blue-700': selected,
                            'text-gray-700 hover:bg-gray-50 dark:text-trueGray-300 dark:hover:bg-trueGray-700': !selected
                        }">
                            {{ $t('Playlist') }}
                        </button>
                    </Tab>
                    <Tab v-slot="{ selected }" as="template">
                        <button :class="{
                            'px-3 py-2 rounded-md': true,
                            'text-blue-700 bg-blue-100 dark:text-white dark:bg-blue-700': selected,
                            'text-gray-700 hover:bg-gray-50 dark:text-trueGray-300 dark:hover:bg-trueGray-700': !selected
                        }">
                            {{ $t('Channel') }}
                        </button>
                    </Tab>
                </TabList>
            </div>
            <div class="px-4 pb-5 sm:px-6 sm:pb-6">
                <TabPanels>
                    <TabPanel>
                        <slot name="video"></slot>
                    </TabPanel>
                    <TabPanel>
                        <slot name="playlist"></slot>
                    </TabPanel>
                    <TabPanel>
                        <slot name="channel"></slot>
                    </TabPanel>
                </TabPanels>
            </div>
            <div class="px-4 py-3 bg-gray-100 dark:bg-trueGray-850 shadow-inner text-right sm:px-6">
                <button type="submit" class="btn btn-primary">
                    {{ $t('Start import') }}
                </button>
            </div>
        </form>
    </TabGroup>
</template>

<script setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import { ref } from 'vue';

const actions = [
    '/admin/videos',
    '/admin/playlists',
    '/admin/channels',
]

// These values are used in the slot templates
const videoIds = ref('')
const playlistIds = ref('')
const channelId = ref('')
const playlists = ref(false)
const videos = ref(false)

const action = ref(actions[0])
function changeTab(index) {
    action.value = actions[index]
}
</script>
