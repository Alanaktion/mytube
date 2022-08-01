<template>
    <button class="btn btn-secondary" @click="open">
        <FilterIcon class="w-4 h-4 mr-1" aria-hidden="true" />
        {{ $t('Filter') }}
    </button>

    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="close" class="relative z-40">
            <TransitionChild as="template" enter="duration-200 ease-out" enter-from="opacity-0" enter-to="opacity-100"
                leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-y-0 right-0 overflow-hidden">
                <div class="flex min-h-full">
                    <TransitionChild
                        as="template"
                        enter="duration-200 ease-out"
                        enter-from="opacity-0 translate-x-3"
                        enter-to="opacity-100 translate-x-0"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 translate-x-0"
                        leave-to="opacity-0 translate-x-3"
                    >
                        <DialogPanel class="w-60 min-h-full transform bg-white dark:bg-neutral-800 shadow-xl transition-all origin-right">
                            <form class="flex flex-col min-h-full" action="" method="get">
                                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-neutral-700 py-2 px-4">
                                    <button class="appearance-none p-2 -ml-2" type="reset" @click="close">
                                        <span class="sr-only">{{ $t('Close') }}</span>
                                        <XIcon class="w-4 h-4" />
                                    </button>
                                    <DialogTitle class="flex-1 text-lg font-medium leading-6 text-slate-700 dark:text-neutral-300">
                                        {{ $t('Filter') }}
                                    </DialogTitle>
                                    <button class="btn btn-primary" type="submit">
                                        {{ $t('Apply') }}
                                    </button>
                                </div>
                                <div class="flex-1 flex flex-col gap-4 lg:gap-6 overflow-y-auto px-2">
                                    <RadioGroup v-model="source" name="source" class="flex flex-col gap-1">
                                        <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100">
                                            {{ $t('Source') }}
                                        </RadioGroupLabel>
                                        <RadioGroupOption
                                            class="btn-focus"
                                            v-slot="{ checked }"
                                            value=""
                                        >
                                            <div
                                                class="flex items-center w-full cursor-pointer btn border-transparent"
                                                :class="{
                                                    'hover:bg-slate-100 hover:dark:bg-neutral-700 text-slate-700 dark:text-neutral-300': !checked,
                                                    'bg-primary-100 hover:bg-primary-200 dark:bg-primary-800 dark:hover:bg-primary-700 text-primary-800 dark:text-primary-50': checked,
                                                }"
                                            >
                                                {{ $t('All Sources') }}
                                                <CheckCircleIcon
                                                    v-if="checked"
                                                    class="w-4 h-4 ml-auto"
                                                    aria-hidden="true"
                                                />
                                            </div>
                                        </RadioGroupOption>
                                        <RadioGroupOption
                                            class="btn-focus"
                                            v-for="(name, key) in sources"
                                            v-slot="{ checked }"
                                            :value="key"
                                        >
                                            <div
                                                class="flex items-center w-full cursor-pointer btn border-transparent"
                                                :class="{
                                                    'hover:bg-slate-100 hover:dark:bg-neutral-700 text-slate-700 dark:text-neutral-300': !checked,
                                                    'bg-primary-100 hover:bg-primary-200 dark:bg-primary-800 dark:hover:bg-primary-700 text-primary-800 dark:text-primary-50': checked,
                                                }"
                                            >
                                                <slot :name="`icon-${key}`"></slot>
                                                {{ name }}
                                                <CheckCircleIcon
                                                    v-if="checked"
                                                    class="w-4 h-4 ml-auto"
                                                    aria-hidden="true"
                                                />
                                            </div>
                                        </RadioGroupOption>
                                    </RadioGroup>
                                    <RadioGroup v-model="files" name="files" class="flex flex-col gap-1">
                                        <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100">
                                            {{ $t('Files') }}
                                        </RadioGroupLabel>
                                        <RadioGroupOption
                                            class="btn-focus"
                                            v-for="option in fileOptions"
                                            v-slot="{ checked }"
                                            :value="option.key"
                                        >
                                            <div
                                                class="flex items-center w-full cursor-pointer btn border-transparent"
                                                :class="{
                                                    'hover:bg-slate-100 hover:dark:bg-neutral-700 text-slate-700 dark:text-neutral-300': !checked,
                                                    'bg-primary-100 hover:bg-primary-200 dark:bg-primary-800 dark:hover:bg-primary-700 text-primary-800 dark:text-primary-50': checked,
                                                }"
                                            >
                                                {{ option.label }}
                                                <CheckCircleIcon
                                                    v-if="checked"
                                                    class="w-4 h-4 ml-auto"
                                                    aria-hidden="true"
                                                />
                                            </div>
                                        </RadioGroupOption>
                                    </RadioGroup>
                                </div>
                            </form>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { ref } from 'vue';
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
    RadioGroup,
    RadioGroupLabel,
    RadioGroupOption,
} from '@headlessui/vue';
import { FilterIcon, CheckCircleIcon, XIcon } from '@heroicons/vue/outline';

const props = defineProps({
    sources: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        default: {},
    },
});

const fileOptions = [
    {key: '', label: `Don't filter by files`},
    {key: '1', label: 'With local files'},
    {key: '0', label: 'Missing local files'},
];

const source = ref(props.params.source || '');
const files = ref(props.params.files || '');

const isOpen = ref(false)

function close() {
    isOpen.value = false
}
function open() {
    isOpen.value = true
}
</script>