<template>
    <Menu as="div" class="relative self-center mb-4 sm:mb-0" v-slot="{ open }">
        <div>
            <MenuButton
                class="btn btn-secondary p-2 rounded-full tooltip-left"
                :class="{ 'btn-secondary-active': open }"
                data-tooltip
                :aria-label="$t('More options')"
            >
                <EllipsisHorizontalIcon class="w-5 h-5" aria-hidden="true" />
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
                class="origin-top-right absolute -right-2 w-40 p-2 mt-1 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
            >
                <MenuItem v-slot="{ active }">
                    <button
                        :class="[
                            active ? 'bg-gradient-to-b from-red-400 to-red-500 text-white text-shadow-px-red' : 'text-slate-700 dark:text-neutral-300',
                            'flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md',
                        ]"
                        @click="deleteOpen = true"
                    >
                        {{ $t('Delete') }}
                    </button>
                </MenuItem>
            </MenuItems>
        </transition>
    </Menu>

    <DeleteVideo :uuid="uuid" v-model:open="deleteOpen" />
</template>

<script setup>
import { ref } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { EllipsisHorizontalIcon } from '@heroicons/vue/20/solid';
import DeleteVideo from './DeleteVideo.vue';

defineProps({
    uuid: {
        type: String,
        required: true,
    },
});

const deleteOpen = ref(false);
</script>
