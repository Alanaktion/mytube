<template>
    <Listbox v-model="value">
        <div class="relative">
            <ListboxButton
                class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-white focus:ring-blue-500 dark:border-trueGray-700 dark:bg-trueGray-800 dark:text-trueGray-300 dark:focus:ring-offset-trueGray-900"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                {{ label }}
                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
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
                    class="origin-top-right absolute right-0 w-40 py-1 mt-2 z-10 rounded-md shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none"
                >
                    <!-- TODO: reduce duplication of code between "all" and list of sources -->
                    <ListboxOption
                        :value="null"
                        v-slot="{ active, selected }"
                        as="template"
                    >
                        <li
                            class="flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer"
                            :class="{
                                'text-gray-700 dark:text-trueGray-300': !selected,
                                'bg-gray-100 dark:bg-trueGray-700': !selected && active,
                                'text-white': selected,
                                'bg-blue-400 dark:bg-blue-600': selected && !active,
                                'bg-blue-500 dark:bg-blue-500': selected && active,
                            }"
                        >
                            {{ allSourcesLabel }}
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

                    <li
                        role="separator"
                        class="border-t border-gray-200 dark:border-trueGray-850 my-1"
                    />

                    <ListboxOption
                        v-for="(name, key) in sources"
                        :key="key"
                        :value="key"
                        v-slot="{ active, selected }"
                        as="template"
                    >
                        <li
                            class="flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer"
                            :class="{
                                'text-gray-700 dark:text-trueGray-300': !selected,
                                'bg-gray-100 dark:bg-trueGray-700': !selected && active,
                                'text-white': selected,
                                'bg-blue-400 dark:bg-blue-600': selected && !active,
                                'bg-blue-500 dark:bg-blue-500': selected && active,
                            }"
                        >
                            <slot :name="`icon-${key}`"></slot>
                            <span>{{ name }}</span>
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
import { setSource } from '../api';

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxOptions,
        ListboxOption,
    },
    props: {
        sources: {
            type: Object,
            required: true,
        },
        value: String,
        label: {
            type: String,
            default: 'Source',
        },
        allSourcesLabel: {
            type: String,
            default: 'All Sources',
        },
    },
    setup(props) {
        const { sources, label, allSourcesLabel } = props;
        const value = ref(props.value || null);

        watch(value, setSource);

        return { value, sources, label, allSourcesLabel };
    },
};
</script>
