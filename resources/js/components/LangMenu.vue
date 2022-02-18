<template>
    <Listbox v-model="lang" v-slot="{ open }">
        <div class="relative">
            <ListboxButton
                class="btn-icon tooltip-left"
                :class="{ 'bg-gray-200 dark:bg-trueGray-800': open }"
                :aria-label="$t('Change language')"
                :data-tooltip="!open"
            >
                <span class="sr-only">{{ $t('Change language') }}</span>
                <svg class="w-4 h-4" viewBox="0 0 52 52" fill="currentColor" aria-hidden="true">
                    <path d="M39,18.67H35.42l-4.2,11.12A29,29,0,0,1,20.6,24.91a28.76,28.76,0,0,0,7.11-14.49h5.21a2,2,0,0,0,0-4H19.67V2a2,2,0,1,0-4,0V6.42H2.41a2,2,0,0,0,0,4H7.63a28.73,28.73,0,0,0,7.1,14.49A29.51,29.51,0,0,1,3.27,30a2,2,0,0,0,.43,4,1.61,1.61,0,0,0,.44-.05,32.56,32.56,0,0,0,13.53-6.25,32,32,0,0,0,12.13,5.9L22.83,52H28l2.7-7.76H43.64L46.37,52h5.22Zm-15.3-8.25a23.76,23.76,0,0,1-6,11.86,23.71,23.71,0,0,1-6-11.86Zm8.68,29.15,4.83-13.83L42,39.57Z" />
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
                    class="origin-bottom-right absolute -right-2 bottom-7 w-40 p-2 mb-2 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-trueGray-800 border dark:border-trueGray-850 focus:outline-none"
                >
                    <ListboxOption
                        v-for="(name, code) in locales"
                        :key="code"
                        :value="code"
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
import { setLanguage } from '../api';
import { CheckCircleIcon } from '@heroicons/vue/outline';

export default {
    components: {
        Listbox,
        ListboxButton,
        ListboxOptions,
        ListboxOption,
        CheckCircleIcon,
    },
    props: {
        locales: {
            type: Object,
            required: true,
        },
    },
    setup(props) {
        const { locales } = props;
        const lang = ref(
            document.documentElement
                .getAttribute('lang')
                .replace('-', '_')
        );

        watch(lang, setLanguage);

        return { lang, locales };
    },
};
</script>
