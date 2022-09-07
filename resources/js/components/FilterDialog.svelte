<button class="btn btn-secondary" on:click={() => (isOpen = true)}>
    <Icon src={Funnel} class="w-4 h-4 mr-1" aria-hidden="true" />
    {$t('Filter')}
</button>

<TransitionRoot appear show={isOpen}>
    <Dialog as="div" on:close={() => (isOpen = false)} class="relative z-40">
        <TransitionChild
            enter="duration-200 ease-out"
            enterFrom="opacity-0"
            enterTo="opacity-100"
            leave="duration-200 ease-in"
            leaveFrom="opacity-100"
            leaveTo="opacity-0"
        >
            <DialogOverlay class="fixed inset-0 bg-black bg-opacity-25" />
        </TransitionChild>

        <div class="fixed inset-y-0 right-0 overflow-hidden">
            <div class="flex h-full">
                <TransitionChild
                    enter="duration-150 ease-out"
                    enterFrom="opacity-0 translate-x-24"
                    enterTo="opacity-100 translate-x-0"
                    leave="duration-150 ease-in"
                    leaveFrom="opacity-100 translate-x-0"
                    leaveTo="opacity-0 translate-x-24"
                >
                    <div class="w-60 h-full overflow-y-auto transform bg-white dark:bg-neutral-800 shadow-xl transition-all origin-right">
                        <form class="flex flex-col h-full" action="" method="get">
                            <div class="flex items-center gap-2 border-b border-slate-200 dark:border-neutral-700 py-2 px-4">
                                <button class="appearance-none p-2 -ml-2 opacity-50 hover:opacity-100 btn-focus" type="reset" on:click={() => (isOpen = false)}>
                                    <span class="sr-only">{$t('Close')}</span>
                                    <Icon src={XMark} class="w-4 h-4" />
                                </button>
                                <DialogTitle class="flex-1 text-lg font-medium leading-6 text-slate-700 dark:text-neutral-300">
                                    {$t('Filter')}
                                </DialogTitle>
                                <button class="btn btn-primary" type="submit">
                                    {$t('Apply')}
                                </button>
                            </div>
                            <div class="flex-1 flex flex-col gap-4 lg:gap-6 pb-4 lg:pb-6 overflow-y-auto px-2">
                                <RadioGroup value={source} name="source" class="flex flex-col gap-1">
                                    <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100 dark:bg-neutral-700">
                                        {$t('Source')}
                                    </RadioGroupLabel>
                                    <RadioGroupOption
                                        class="btn-focus"
                                        let:checked
                                        value=""
                                    >
                                        <div class={`flex items-center w-full cursor-pointer btn border-transparent ${checked ? 'bg-primary-100 hover:bg-primary-200 dark:bg-primary-800 dark:hover:bg-primary-700 text-primary-800 dark:text-primary-50' : 'hover:bg-slate-100 hover:dark:bg-neutral-700 text-slate-700 dark:text-neutral-300'}`}>
                                            {$t('All Sources')}
                                            {#if checked}
                                            <Icon src={CheckCircle} theme="mini" class="w-5 h-5 ml-auto" aria-hidden="true" />
                                            {/if}
                                        </div>
                                    </RadioGroupOption>
                                    {#each sourceList as {key, name}}
                                    <RadioGroupOption
                                        class="btn-focus"
                                        let:checked
                                        value={key}
                                    >
                                        <div class={`flex items-center w-full cursor-pointer btn border-transparent ${checked ? 'bg-primary-100 hover:bg-primary-200 dark:bg-primary-800 dark:hover:bg-primary-700 text-primary-800 dark:text-primary-50' : 'hover:bg-slate-100 hover:dark:bg-neutral-700 text-slate-700 dark:text-neutral-300'}`}>
                                            <!-- TODO: Placeholder for dynamically injecting the source icon SVG -->
                                            {{ name }}
                                            {#if checked}
                                            <Icon src={CheckCircle} theme="mini" class="w-5 h-5 ml-auto" aria-hidden="true" />
                                            {/if}
                                        </div>
                                    </RadioGroupOption>
                                    {/each}
                                </RadioGroup>
                                <RadioGroup value={files} name="files" class="flex flex-col gap-1">
                                    <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100 dark:bg-neutral-700">
                                        {$t('Files')}
                                    </RadioGroupLabel>
                                    {#each fileOptions as option}
                                    <RadioGroupOption
                                        class="btn-focus"
                                        let:checked
                                        value={option.key}
                                    >
                                        <div class={`flex items-center w-full cursor-pointer btn border-transparent ${checked ? 'bg-primary-100 hover:bg-primary-200 dark:bg-primary-800 dark:hover:bg-primary-700 text-primary-800 dark:text-primary-50' : 'hover:bg-slate-100 hover:dark:bg-neutral-700 text-slate-700 dark:text-neutral-300'}`}>
                                            {$t(option.label)}
                                            {#if checked}
                                            <Icon src={CheckCircle} theme="mini" class="w-5 h-5 ml-auto" aria-hidden="true" />
                                            {/if}
                                        </div>
                                    </RadioGroupOption>
                                    {/each}
                                </RadioGroup>
                                <RadioGroup value={resolution} name="resolution" class="flex flex-col gap-1">
                                    <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100 dark:bg-neutral-700">
                                        {$t('Resolution')}
                                    </RadioGroupLabel>
                                    {#each resolutionOptions as option}
                                    <RadioGroupOption
                                        class="btn-focus"
                                        let:checked
                                        value={option.key}
                                    >
                                        <div class={`flex items-center w-full cursor-pointer btn border-transparent ${checked ? 'bg-primary-100 hover:bg-primary-200 dark:bg-primary-800 dark:hover:bg-primary-700 text-primary-800 dark:text-primary-50' : 'hover:bg-slate-100 hover:dark:bg-neutral-700 text-slate-700 dark:text-neutral-300'}`}>
                                            {$t(option.label)}
                                            {#if checked}
                                            <Icon src={CheckCircle} theme="mini" class="w-5 h-5 ml-auto" aria-hidden="true" />
                                            {/if}
                                        </div>
                                    </RadioGroupOption>
                                    {/each}
                                </RadioGroup>
                                <RadioGroup value={mimeType} name="mime_type" class="flex flex-col gap-1">
                                    <RadioGroupLabel class="form-label px-4 py-3 -mx-2 mb-1 bg-gray-100 dark:bg-neutral-700">
                                        {$t('Format')}
                                    </RadioGroupLabel>
                                    {#each mimeTypeOptions as option}
                                    <RadioGroupOption
                                        class="btn-focus"
                                        let:checked
                                        value={option.key}
                                    >
                                        <div class={`flex items-center w-full cursor-pointer btn border-transparent ${checked ? 'bg-primary-100 hover:bg-primary-200 dark:bg-primary-800 dark:hover:bg-primary-700 text-primary-800 dark:text-primary-50' : 'hover:bg-slate-100 hover:dark:bg-neutral-700 text-slate-700 dark:text-neutral-300'}`}>
                                            {$t(option.label)}
                                            {#if checked}
                                            <Icon src={CheckCircle} theme="mini" class="w-5 h-5 ml-auto" aria-hidden="true" />
                                            {/if}
                                        </div>
                                    </RadioGroupOption>
                                    {/each}
                                </RadioGroup>
                            </div>
                        </form>
                    </div>
                </TransitionChild>
            </div>
        </div>
    </Dialog>
</TransitionRoot>

<script>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogTitle,
    RadioGroup,
    RadioGroupLabel,
    RadioGroupOption,
} from '@rgossiaux/svelte-headlessui';
import { Icon } from '@steeze-ui/svelte-icon';
import { CheckCircle, Funnel, XMark } from '@steeze-ui/heroicons';
import { t } from 'svelte-i18n';

export let sources = {};

// Convert source map to iterable array
$: sourceList = Object.keys(sources).map(key => {
    return {
        key,
        name: sources[key],
    };
});

export let params = {};

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

let source = params.source || '';
let files = params.files || '';
let resolution = params.resolution || '';
let mimeType = params.mime_type || '';

// TODO: Add a bunch of stupid stuff to track the state of the dialog because Svelte doesn't have simple watchers
// $: if (files !== '1') {
//     resolution = '';
//     mimeType = '';
// }

// $: if (resolution !== '') {
//     files = '1';
// }

// $: if (mimeType !== '') {
//     files = '1';
// }

let isOpen = false;
</script>
