<template>
    <Listbox v-model="value" v-slot="{ open }">
        <div class="relative">
            <ListboxButton class="btn btn-secondary" :class="{ 'btn-secondary-active': open }">
                <BarsArrowDownIcon class="w-4 h-4 mr-1" aria-hidden="true" />
                {{ $t('Sort') }}
                <ChevronDownIcon class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
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
                    class="origin-top-right sm:origin-top-right absolute -left-2 sm:left-auto sm:-right-2 w-48 p-2 mt-1 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border border-slate-400 dark:border-neutral-850 focus:outline-hidden"
                >
                    <ListboxOption
                        v-for="(name, key) in options"
                        :key="key"
                        :value="key"
                        v-slot="{ active, selected }"
                        as="template"
                    >
                        <li
                            class="flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md"
                            :class="{
                                'text-slate-700 dark:text-neutral-300': !selected,
                                'bg-slate-100 dark:bg-neutral-700': !selected && active,
                                'text-white text-shadow-px-primary': selected,
                                'bg-linear-to-b from-primary-400 to-primary-500 dark:from-primary-600 dark:to-primary-700': selected && !active,
                                'bg-linear-to-b from-primary-500 to-primary-600 dark:from-primary-500 dark:to-primary-600': selected && active,
                            }"
                        >
                            <slot :name="`icon-${key}`"></slot>
                            <span>{{ $t(name) }}</span>
                            <CheckCircleIcon
                                v-if="selected"
                                class="w-5 h-5 ml-auto"
                                aria-hidden="true"
                            />
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

<script setup>
import { ref, watch } from 'vue';
import {
    Listbox,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from "@headlessui/vue";
import { setSort } from '../api';
import { CheckCircleIcon, ChevronDownIcon } from '@heroicons/vue/20/solid';
import { BarsArrowDownIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    value: String,
});

const options = {
    published_at: 'Published date',
    created_at: 'Imported date',
};
const value = ref(props.value || null);

watch(value, setSort);
</script>
