<Listbox value={value} let:open>
    <div class="relative">
        <ListboxButton class={`btn btn-secondary ${open && 'btn-secondary-active'}`}>
            <Icon src={BarsArrowDown} class="w-4 h-4 mr-1" aria-hidden="true" />
            {$t('Sort')}
            <Icon src={ChevronDown} theme="mini" class="-mr-1 ml-2 h-5 w-5" aria-hidden="true" />
        </ListboxButton>
        <Transition
            enter="transition duration-100 ease-out"
            enterFrom="scale-95 opacity-0"
            enterTo="scale-100 opacity-100"
            leave="transition duration-75 ease-in"
            leaveFrom="scale-100 opacity-100"
            leaveTo="scale-95 opacity-0"
        >
            <ListboxOptions
                class="origin-top-right sm:origin-top-right absolute -left-2 sm:left-auto sm:-right-2 w-48 p-2 mt-1 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
            >
                {#each options as name, key}
                <ListboxOption value={key} let:active let:selected>
                    <li
                        class={`flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md ${selected ? `text-white text-shadow-px-primary ${active ? 'bg-gradient-to-b from-primary-500 to-primary-600 dark:from-primary-500 dark:to-primary-600' : 'bg-gradient-to-b from-primary-400 to-primary-500 dark:from-primary-600 dark:to-primary-700'}` : 'text-slate-700 dark:text-neutral-300'} ${!selected && active ? 'bg-slate-100 dark:bg-neutral-700' : ''}`}
                    >
                        <!-- TODO: Placeholder for dynamically injecting the source icon SVG -->
                        <span>{$t(name)}</span>
                        {#if selected}
                        <Icon src={CheckCircle} theme="mini" class="w-5 h-5 ml-auto" aria-hidden="true" />
                        {/if}
                    </li>
                </ListboxOption>
                {/each}
            </ListboxOptions>
        </Transition>
    </div>
</Listbox>

<script>
import {
    Listbox,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
    Transition,
} from '@rgossiaux/svelte-headlessui';
import { setSort } from '../api';
import { Icon } from '@steeze-ui/svelte-icon';
import { BarsArrowDown, CheckCircle, ChevronDown } from '@steeze-ui/heroicons';
import { t } from 'svelte-i18n';

export let value;
let previousValue = value;

// TODO: make this an iterable array of objects
const options = {
    published_at: 'Published date',
    created_at: 'Imported date',
};

$: if (value !== previousValue) {
    setSort(value);
    previousValue = value;
}
</script>
