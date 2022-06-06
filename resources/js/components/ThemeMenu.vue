<template>
    <Listbox v-model="currentTheme" v-slot="{ open }">
        <div class="relative">
            <ListboxButton
                class="btn-icon tooltip-left"
                :class="{ 'bg-gray-200 dark:bg-trueGray-800': open }"
                :aria-label="$t('Toggle Dark Theme')"
                :data-tooltip="!open"
            >
                <span class="sr-only">{{ $t('Toggle Dark Theme') }}</span>
                <MoonIcon class="w-4 h-4" aria-hidden="true" />
            </ListboxButton>
            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <ListboxOptions
                    class="origin-bottom-right absolute -right-2 bottom-7 w-40 p-2 mb-2 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none"
                >
                    <ListboxOption
                        v-for="theme in themes"
                        :key="theme"
                        :value="theme"
                        v-slot="{ active, selected }"
                        as="template"
                    >
                        <li
                            class="flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md"
                            :class="{
                                'text-gray-700 dark:text-trueGray-300': !selected,
                                'bg-gray-100 dark:bg-trueGray-700': !selected && active,
                                'text-white': selected,
                                'bg-gradient-to-b from-blue-400 to-blue-500 dark:from-blue-600 dark:to-blue-700': selected && !active,
                                'bg-gradient-to-b from-blue-500 to-blue-600 dark:from-blue-500 dark:to-blue-600': selected && active,
                            }"
                        >
                            <span>{{ $t(`theme.${theme}`) }}</span>
                            <CheckCircleIcon
                                v-if="selected"
                                class="w-4 h-4 ml-auto"
                                aria-hidden="true"
                            />
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
import { MoonIcon } from '@heroicons/vue/solid';
import { CheckCircleIcon } from '@heroicons/vue/outline';

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxOptions,
        ListboxOption,
        MoonIcon,
        CheckCircleIcon,
    },
    setup() {
        const themes = ['auto', 'light', 'dark'];
        const currentTheme = ref(localStorage.theme || 'auto');

        watch(currentTheme, setTheme);

        return { currentTheme, themes };
    },
};
</script>
