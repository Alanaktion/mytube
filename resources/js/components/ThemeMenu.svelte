<Listbox value={currentTheme} on:change={e => (currentTheme = e.detail)} let:open>
    <div class="relative">
        <ListboxButton
            class={`btn-icon tooltip-left ${open ? 'bg-slate-200 dark:bg-neutral-800' : ''}`}
            aria-label={$t('Toggle Dark Theme')}
            data-tooltip={!open}
        >
            <span class="sr-only">{$t('Toggle Dark Theme')}</span>
            <Icon src={Moon} theme="mini" class="w-4 h-4" aria-hidden="true" />
        </ListboxButton>
        <Transition
            class="z-30"
            enter="transition duration-100 ease-out"
            enterFrom="scale-95 opacity-0"
            enterTo="scale-100 opacity-100"
            leave="transition duration-75 ease-in"
            leaveFrom="scale-100 opacity-100"
            leaveTo="scale-95 opacity-0"
        >
            <ListboxOptions
                class="origin-bottom-right absolute -right-2 bottom-7 w-40 p-2 mb-2 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
            >
                {#each themes as theme}
                <ListboxOption value={theme} let:active let:selected>
                    <li
                        class={`flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md ${selected ? `text-white text-shadow-px-primary ${active ? 'bg-gradient-to-b from-primary-500 to-primary-600 dark:from-primary-500 dark:to-primary-600' : 'bg-gradient-to-b from-primary-400 to-primary-500 dark:from-primary-600 dark:to-primary-700'}` : 'text-slate-700 dark:text-neutral-300'} ${!selected && active ? 'bg-slate-100 dark:bg-neutral-700' : ''}`}
                    >
                        <span>{$t(`theme.${theme}`)}</span>
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
} from "@rgossiaux/svelte-headlessui";
import { setTheme } from '../api';
import { Icon } from '@steeze-ui/svelte-icon';
import { CheckCircle, Moon } from '@steeze-ui/heroicons';
import { t } from 'svelte-i18n';

const themes = ['auto', 'light', 'dark'];
let currentTheme = localStorage.theme || 'auto';
let previousTheme = currentTheme;

$: if (currentTheme !== previousTheme) {
    setTheme(currentTheme);
    previousTheme = currentTheme;
}
</script>
