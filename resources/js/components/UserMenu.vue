<template>
    <Menu as="div" class="relative self-center mb-4 sm:mb-0">
        <div>
            <MenuButton
                class="flex px-3 py-2 rounded-md text-sm font-medium focus:text-white focus:bg-gray-700 dark:focus:bg-trueGray-700 text-gray-300 dark:text-trueGray-300 hover:text-white hover:bg-gray-700 dark:hover:bg-trueGray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-blue-500 dark:focus:ring-offset-trueGray-800 dark:focus:ring-blue-600"
            >
                {{ name }}
                <ChevronDownIcon class="w-5 h-5 ml-2 -mr-1 text-gray-300 dark:text-trueGray-300 hover:text-white" />
            </MenuButton>
        </div>

        <form class="hidden" method="POST" action="/logout" aria-hidden="true" ref="form">
            <input type="hidden" name="_token" v-model="token">
            <button type="submit"></button>
        </form>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="origin-bottom-right absolute right-0 w-40 py-1 mt-2 z-10 rounded-md shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none"
            >
                <!-- <div class="px-1 py-1"> -->
                    <MenuItem v-slot="{ active }">
                        <a
                            href="/user/account"
                            :class="[
                                active ? 'bg-blue-500 text-white dark:bg-blue-500' : 'text-gray-700 dark:text-trueGray-300',
                                'flex items-center appearance-none w-full px-4 py-2 text-sm capitalize cursor-pointer',
                            ]"
                        >
                            Account
                        </a>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <button
                            @click="logOut"
                            :class="[
                                active ? 'bg-blue-500 text-white dark:bg-blue-500' : 'text-gray-700 dark:text-trueGray-300',
                                'flex items-center appearance-none w-full px-4 py-2 text-sm capitalize cursor-pointer',
                            ]"
                        >
                            Log out
                        </button>
                    </MenuItem>
                <!-- </div> -->
            </MenuItems>
        </transition>
    </Menu>
</template>

<script>
import { ref } from 'vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/outline';

export default {
    components: {
        Menu,
        MenuButton,
        MenuItems,
        MenuItem,
        ChevronDownIcon,
    },
    props: {
        name: {
            type: String,
            required: true,
        },
        token: {
            type: String,
            required: true,
        },
    },
    setup() {
        const form = ref(null);
        const logOut = () => {
            form.value && form.value.submit();
        };

        return {
            form,
            logOut,
        };
    },
}
</script>
