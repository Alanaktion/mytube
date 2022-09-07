<Transition appear show={open}>
    <Dialog on:close={close} class="relative z-40">
        <TransitionChild
            enter="duration-200 ease-out"
            enterFrom="opacity-0"
            enterTo="opacity-100"
            leave="duration-150 ease-in"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
        >
            <DialogOverlay class="fixed inset-0 bg-black bg-opacity-25" />
        </TransitionChild>

        <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <TransitionChild
                    enter="duration-200 ease-out"
                    enterFrom="opacity-0 scale-95"
                    enterTo="opacity-100 scale-100"
                    leave="duration-150 ease-in"
                    leaveFrom="opacity-100 scale-100"
                    leaveTo="opacity-0 scale-95"
                >
                    <div class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-neutral-800 p-6 text-left align-middle shadow-xl transition-all">
                        <div class="flex items-center justify-between mb-2" v-if="title">
                            <DialogTitle as="h3" class="text-lg font-medium leading-6 text-slate-700 dark:text-neutral-300">
                                {title}
                            </DialogTitle>
                            <button class="appearance-none p-2 -ml-2 opacity-50 hover:opacity-100 btn-focus" type="reset" on:click={close}>
                                <span class="sr-only">{$t('Close')}</span>
                                <Icon src={XMark} theme="outline" size="24" class="w-4 h-4" />
                            </button>
                        </div>
                        <slot />
                    </div>
                </TransitionChild>
            </div>
        </div>
    </Dialog>
</Transition>

<script>
import {
    Transition,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogTitle,
} from '@rgossiaux/svelte-headlessui';
import { Icon } from '@steeze-ui/svelte-icon';
import { XMark } from '@steeze-ui/heroicons';
import { t } from 'svelte-i18n';

export let open = false;
export let title = null;

import { createEventDispatcher } from 'svelte';

const dispatch = createEventDispatcher();
function close() {
    open = false;
    dispatch('close');
}
</script>
