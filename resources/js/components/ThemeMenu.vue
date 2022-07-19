<template>
    <Listbox v-model="currentTheme" v-slot="{ open }">
        <div class="relative">
            <ListboxButton
                class="btn-icon tooltip-left"
                :class="{ 'bg-slate-200 dark:bg-neutral-800': open }"
                :aria-label="$t('Toggle Dark Theme')"
                :data-tooltip="!open"
            >
                <span class="sr-only">{{ $t('Toggle Dark Theme') }}</span>
                <MoonIcon class="w-4 h-4" aria-hidden="true" />
            </ListboxButton>
            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="scale-95 opacity-0"
                enter-to-class="scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="scale-100 opacity-100"
                leave-to-class="scale-95 opacity-0"
            >
                <ListboxOptions
                    class="origin-bottom-right absolute -right-2 bottom-7 w-40 p-2 mb-2 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
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
                                'text-slate-700 dark:text-neutral-300': !selected,
                                'bg-slate-100 dark:bg-neutral-700': !selected && active,
                                'text-white text-shadow-px-primary': selected,
                                'bg-gradient-to-b from-primary-400 to-primary-500 dark:from-primary-600 dark:to-primary-700': selected && !active,
                                'bg-gradient-to-b from-primary-500 to-primary-600 dark:from-primary-500 dark:to-primary-600': selected && active,
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
