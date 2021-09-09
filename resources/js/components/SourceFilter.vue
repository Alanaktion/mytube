<template>
    <Listbox v-model="value">
        <div class="relative">
            <ListboxButton
                class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-offset-white focus:ring-blue-500 dark:border-trueGray-700 dark:bg-trueGray-800 dark:text-trueGray-300 dark:focus:ring-offset-trueGray-900"
            >
                <FilterIcon class="w-4 h-4 mr-1" aria-hidden="true" />
                {{ label }}
                <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
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
                    class="origin-top-left sm:origin-top-right absolute left-0 sm:left-auto sm:right-0 w-40 py-1 mt-1 z-10 rounded-md shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none"
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
                            <CheckCircleIcon
                                v-if="selected"
                                class="w-4 h-4 ml-auto"
                                aria-hidden="true"
                            />
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
import { setSource } from '../api';
import { CheckCircleIcon, ChevronDownIcon, FilterIcon } from '@heroicons/vue/outline';

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxOptions,
        ListboxOption,
        CheckCircleIcon,
        ChevronDownIcon,
        FilterIcon,
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
