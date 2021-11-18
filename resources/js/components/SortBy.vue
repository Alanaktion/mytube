<template>
    <Listbox v-model="value">
        <div class="relative">
            <ListboxButton class="btn btn-secondary">
                <SortDescendingIcon class="w-4 h-4 mr-1" aria-hidden="true" />
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
                    class="origin-top-left sm:origin-top-right absolute left-0 sm:left-auto sm:right-0 w-40 py-1 mt-1 z-30 rounded-md shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none"
                >
                    <ListboxOption
                        v-for="(name, key) in options"
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
                                'bg-gradient-to-b from-blue-400 to-blue-500 dark:from-blue-600 dark:to-blue-700': selected && !active,
                                'bg-gradient-to-b from-blue-500 to-blue-600 dark:from-blue-500 dark:to-blue-600': selected && active,
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
import { setSort } from '../api';
import { CheckCircleIcon, ChevronDownIcon, SortDescendingIcon } from '@heroicons/vue/outline';

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxOptions,
        ListboxOption,
        CheckCircleIcon,
        SortDescendingIcon,
        ChevronDownIcon,
    },
    props: {
        value: String,
        options: {
            type: Object,
            default: {
                published_at: 'Published',
                created_at: 'Imported',
            },
        },
        label: {
            type: String,
            default: 'Sort',
        },
    },
    setup(props) {
        const { options, label } = props;
        const value = ref(props.value || null);

        watch(value, setSort);

        return { value, options, label };
    },
};
</script>
