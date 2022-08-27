<template>
    <TransitionRoot appear :show="open" as="template">
        <Dialog as="div" @close="$emit('close')" class="relative z-40">
            <TransitionChild
                as="template"
                enter="duration-200 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-150 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black bg-opacity-25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-200 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-150 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-neutral-800 p-6 text-left align-middle shadow-xl transition-all">
                            <div class="flex items-center justify-between mb-2" v-if="title">
                                <DialogTitle as="h3" class="text-lg font-medium leading-6 text-slate-700 dark:text-neutral-300">
                                    {{ title }}
                                </DialogTitle>
                                <button class="appearance-none p-2 -ml-2 opacity-50 hover:opacity-100 btn-focus" type="reset" @click="$emit('close')">
                                    <span class="sr-only">{{ $t('Close') }}</span>
                                    <XMarkIcon class="w-4 h-4" />
                                </button>
                            </div>
                            <slot />
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';

defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        required: false,
    },
});

defineEmits(['close']);
</script>
