<template>
    <Listbox v-model="currentTheme">
        <div class="relative">
            <ListboxButton
                class="p-2 rounded-full text-sm font-medium text-blue-600 focus:bg-gray-200 dark:focus:bg-trueGray-800 dark:text-blue-400 hover:bg-gray-200 dark:hover:bg-trueGray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-trueGray-900 dark:focus:ring-blue-600 tooltip-left"
                :title="label"
            >
                <span class="sr-only">{{ label }}</span>
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
            </ListboxButton>
            <transition
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions
                    class="origin-bottom-right absolute right-0 bottom-7 w-40 py-1 mb-2 z-10 rounded-md shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none"
                >
                    <ListboxOption
                        v-for="theme in themes"
                        :key="theme"
                        :value="theme"
                        v-slot="{ active, selected }"
                        as="template"
                    >
                        <li
                            class="flex items-center appearance-none w-full px-4 py-2 text-sm capitalize cursor-pointer"
                            :class="{
                                'text-gray-700 dark:text-trueGray-300': !selected,
                                'bg-gray-100 dark:bg-trueGray-700': !selected && active,
                                'text-white': selected,
                                'bg-blue-400 dark:bg-blue-600': selected && !active,
                                'bg-blue-500 dark:bg-blue-500': selected && active,
                            }"
                        >
                            <span>{{ theme }}</span>
                            <svg
                                v-if="selected"
                                class="w-4 h-4 ml-auto"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-hidden="true"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

<script>
import { ref, watch } from 'vue';
import {
    Listbox,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from "@headlessui/vue";
import { setTheme } from '../api';

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxOptions,
        ListboxOption,
    },
    props: {
        label: {
            type: String,
            default: 'Language',
        },
    },
    setup(props) {
        const { label } = props;
        const themes = ['auto', 'light', 'dark'];
        const currentTheme = ref(localStorage.theme || 'auto');

        watch(currentTheme, setTheme);

        return { currentTheme, themes, label };
    },
};
</script>
