<template>
    <Menu as="div" class="relative self-center mb-4 sm:mb-0" v-slot="{ open }">
        <div>
            <MenuButton
                class="flex px-3 py-2 w-full sm:w-auto rounded-md text-sm font-medium focus:text-white focus:bg-slate-700 dark:focus:bg-neutral-700 text-slate-300 dark:text-neutral-300 hover:text-white hover:bg-slate-700 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 focus:ring-primary-500 dark:focus:ring-offset-neutral-800 dark:focus:ring-primary-600"
                :class="{ 'bg-slate-700 dark:bg-neutral-700': open }"
            >
                <UserCircleIcon class="hidden md:block lg:hidden w-5 h-5" aria-hidden="true" />
                <span class="md:sr-only lg:not-sr-only">{{ name }}</span>
                <ChevronDownIcon class="md:hidden lg:block w-5 h-5 ml-auto sm:ml-2 -mr-1" aria-hidden="true" />
            </MenuButton>
        </div>

        <form class="hidden" method="POST" action="/logout" aria-hidden="true" ref="form">
            <input type="hidden" name="_token" v-model="token">
            <button type="submit"></button>
        </form>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
        >
            <MenuItems
                class="origin-top-right absolute -left-2 sm:left-auto sm:-right-2 w-40 p-2 mt-2 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
            >
                <MenuItem v-slot="{ active }">
                    <a
                        href="/users"
                        :class="[
                            active ? 'bg-gradient-to-b from-primary-500 to-primary-600 text-white text-shadow-px-primary' : 'text-slate-700 dark:text-neutral-300',
                            'flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md',
                        ]"
                    >
                        {{ $t('Account') }}
                    </a>
                </MenuItem>
                <MenuItem v-slot="{ active }" v-if="admin">
                    <a
                        href="/admin"
                        :class="[
                            active ? 'bg-gradient-to-b from-primary-500 to-primary-600 text-white text-shadow-px-primary' : 'text-slate-700 dark:text-neutral-300',
                            'flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md',
                        ]"
                    >
                        {{ $t('Administration') }}
                    </a>
                </MenuItem>
                <MenuItem v-slot="{ active }">
                    <button
                        @click="logOut"
                        :class="[
                            active ? 'bg-gradient-to-b from-primary-500 to-primary-600 text-white text-shadow-px-primary' : 'text-slate-700 dark:text-neutral-300',
                            'flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md',
                        ]"
                    >
                        {{ $t('Log out') }}
                    </button>
                </MenuItem>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script setup>
import { ref } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { ChevronDownIcon, UserCircleIcon } from '@heroicons/vue/20/solid';

defineProps({
    name: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
    admin: {
        type: Boolean,
        default: false,
    },
});

const form = ref(null);
const logOut = () => {
    form.value && form.value.submit();
};
</script>
