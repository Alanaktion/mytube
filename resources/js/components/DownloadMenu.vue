<template>
    <Menu as="div" class="relative self-center mb-4 sm:mb-0" v-slot="{ open }">
        <div>
            <MenuButton class="btn btn-secondary rounded-full" :class="{ 'btn-secondary-active': open }">
                <span class="md:sr-only lg:not-sr-only">{{ $t('Download') }}</span>
                <DownloadIcon class="md:hidden lg:block w-5 h-5 ml-auto sm:ml-2 -mr-1" aria-hidden="true" />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
        >
            <MenuItems
                class="origin-top-right absolute -left-2 sm:left-auto sm:-right-2 min-w-40 p-2 mt-2 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
            >
                <MenuItem v-slot="{ active }" v-for="file in files" :key="file.id">
                    <a
                        :href="file.url"
                        :class="[
                            active ? 'bg-gradient-to-b from-primary-500 to-primary-600 text-white text-shadow-px-primary' : 'text-slate-700 dark:text-neutral-300',
                            'flex justify-between gap-4 items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md whitespace-nowrap',
                        ]"
                        download
                    >
                        <span v-if="file.height">
                            {{ file.height }}p {{ file.mime_type.substring(6).toUpperCase() }}
                        </span>
                        <span v-else>
                            {{ formatType(file.mime_type) }}
                        </span>
                        <span :class="[
                            active ? 'text-slate-100' : 'text-slate-500 dark:text-neutral-500',
                        ]" v-if="file.size">
                            {{ formatSize(file.size) }}
                        </span>
                    </a>
                </MenuItem>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script setup>
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { DownloadIcon } from '@heroicons/vue/outline';

defineProps({
    files: {
        type: Array,
        required: true,
    },
})

const formatSize = (size) => {
    if (size > 1e9) {
        return `${(size / 1e9).toFixed(2)} GB`;
    }
    if (size > 1e6) {
        return `${(size / 1e6).toFixed(2)} MB`;
    }
    return `${(size / 1e3).toFixed(2)} KB`;
}

const formatType = mime_type => {
    const type = mime_type.split('/')[1];
    return {
        'x-matroska': 'MKV'
    }[type] || type.toUpperCase();
}
</script>
