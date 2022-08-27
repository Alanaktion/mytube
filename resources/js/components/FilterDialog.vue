<template>
    <button class="btn btn-secondary" @click="open">
        <FunnelIcon class="w-4 h-4 mr-1" aria-hidden="true" />
        {{ $t('Filter') }}
    </button>

    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="close" class="relative z-40">
            <TransitionChild as="template" enter="duration-200 ease-out" enter-from="opacity-0" enter-to="opacity-100"
                leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-y-0 right-0 overflow-hidden">
                <div class="flex h-full">
                    <TransitionChild
                        as="template"
                        enter="duration-150 ease-out"
                        enter-from="opacity-0 translate-x-24"
                        enter-to="opacity-100 translate-x-0"
                        leave="duration-150 ease-in"
                        leave-from="opacity-100 translate-x-0"
                        leave-to="opacity-0 translate-x-24"
                    >
                        <DialogPanel class="w-60 h-full overflow-y-auto transform bg-white dark:bg-neutral-800 shadow-xl transition-all origin-right">
                            <form class="flex flex-col h-full" action="" method="get">
                                <div class="flex items-center gap-2 border-b border-slate-200 dark:border-neutral-700 py-2 px-4">
                                    <button class="appearance-none p-2 -ml-2 opacity-50 hover:opacity-100 btn-focus" type="reset" @click="close">
                                        <span class="sr-only">{{ $t('Close') }}</span>
                                        <XMarkIcon class="w-4 h-4" />
                                    </button>
                                    <DialogTitle class="flex-1 text-lg font-medium leading-6 text-slate-700 dark:text-neutral-300">
                                        {{ $t('Filter') }}
                                    </DialogTitle>
                                    <button class="btn btn-primary" type="submit">
                                        {{ $t('Apply') }}
                                    </button>
                                </div>
                                <div class="flex-1 flex flex-col gap-4 lg:gap-6 pb-4 lg:pb-6 overflow-y-auto px-2">
                                    <RadioGroup v-model="source" name="source" class="flex flex-col gap-1">
                                        <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100 dark:bg-neutral-700">
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
                                                    class="w-5 h-5 ml-auto"
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
                                                    class="w-5 h-5 ml-auto"
                                                    aria-hidden="true"
                                                />
                                            </div>
                                        </RadioGroupOption>
                                    </RadioGroup>
                                    <RadioGroup v-model="files" name="files" class="flex flex-col gap-1">
                                        <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100 dark:bg-neutral-700">
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
                                                {{ $t(option.label) }}
                                                <CheckCircleIcon
                                                    v-if="checked"
                                                    class="w-5 h-5 ml-auto"
                                                    aria-hidden="true"
                                                />
                                            </div>
                                        </RadioGroupOption>
                                    </RadioGroup>
                                    <RadioGroup v-model="resolution" name="resolution" class="flex flex-col gap-1">
                                        <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100 dark:bg-neutral-700">
                                            {{ $t('Resolution') }}
                                        </RadioGroupLabel>
                                        <RadioGroupOption
                                            class="btn-focus"
                                            v-for="option in resolutionOptions"
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
                                                {{ $t(option.label) }}
                                                <CheckCircleIcon
                                                    v-if="checked"
                                                    class="w-5 h-5 ml-auto"
                                                    aria-hidden="true"
                                                />
                                            </div>
                                        </RadioGroupOption>
                                    </RadioGroup>
                                    <RadioGroup v-model="mimeType" name="mime_type" class="flex flex-col gap-1">
                                        <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100 dark:bg-neutral-700">
                                            {{ $t('Format') }}
                                        </RadioGroupLabel>
                                        <RadioGroupOption
                                            class="btn-focus"
                                            v-for="option in mimeTypeOptions"
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
                                                {{ $t(option.label) }}
                                                <CheckCircleIcon
                                                    v-if="checked"
                                                    class="w-5 h-5 ml-auto"
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
import { ref, watch } from 'vue';
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
import { CheckCircleIcon } from '@heroicons/vue/20/solid';
import { FunnelIcon, XMarkIcon } from '@heroicons/vue/24/outline';

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
    {key: '', label: 'Any'},
    {key: '1', label: 'With local files'},
    {key: '0', label: 'Missing local files'},
];
const resolutionOptions = [
    {key: '', label: 'Any'},
    {key: '480', label: '480p'},
    {key: '720', label: '720p'},
    {key: '1080', label: '1080p'},
    {key: '1440', label: '1440p'},
    {key: '2160', label: '2160p'},
    {key: '4320', label: '4320p'},
    {key: 'portrait', label: 'Portrait'},
];
const mimeTypeOptions = [
    {key: '', label: 'Any'},
    {key: 'video/mp4', label: 'MP4'},
    {key: 'video/webm', label: 'WebM'},
    {key: 'video/x-matroska', label: 'MKV'},
];

const source = ref(props.params.source || '');
const files = ref(props.params.files || '');
const resolution = ref(props.params.resolution || '');
const mimeType = ref(props.params.mime_type || '');

watch(files, () => {
    if (files.value !== '1') {
        resolution.value = '';
        mimeType.value = '';
    }
}, { immediate: true });
watch(resolution, () => {
    if (resolution.value !== '') {
        files.value = '1';
    }
}, { immediate: true });
watch(mimeType, () => {
    if (mimeType.value !== '') {
        files.value = '1';
    }
}, { immediate: true });

const isOpen = ref(false)

function close() {
    isOpen.value = false
}
function open() {
    isOpen.value = true
}
</script>
