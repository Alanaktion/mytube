<Listbox value={lang} on:change={e => (lang = e.detail)} let:open>
    <div class="relative">
        <ListboxButton
            class={`btn-icon tooltip-left ${open ? 'bg-slate-200 dark:bg-neutral-800' : ''}`}
            aria-label={$t('Change language')}
            data-tooltip={!open}
        >
            <span class="sr-only">{$t('Change language')}</span>
            <svg class="w-4 h-4" viewBox="0 0 52 52" fill="currentColor" aria-hidden="true">
                <path d="M39,18.67H35.42l-4.2,11.12A29,29,0,0,1,20.6,24.91a28.76,28.76,0,0,0,7.11-14.49h5.21a2,2,0,0,0,0-4H19.67V2a2,2,0,1,0-4,0V6.42H2.41a2,2,0,0,0,0,4H7.63a28.73,28.73,0,0,0,7.1,14.49A29.51,29.51,0,0,1,3.27,30a2,2,0,0,0,.43,4,1.61,1.61,0,0,0,.44-.05,32.56,32.56,0,0,0,13.53-6.25,32,32,0,0,0,12.13,5.9L22.83,52H28l2.7-7.76H43.64L46.37,52h5.22Zm-15.3-8.25a23.76,23.76,0,0,1-6,11.86,23.71,23.71,0,0,1-6-11.86Zm8.68,29.15,4.83-13.83L42,39.57Z" />
            </svg>
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
                class="origin-bottom-right absolute -right-2 bottom-7 w-40 p-2 mb-2 z-30 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
            >
                {#each localeList as {name, code}, i}
                <ListboxOption value={code} let:active let:selected>
                    <li
                        class={`flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md ${selected ? `text-white text-shadow-px-primary ${active ? 'bg-gradient-to-b from-primary-500 to-primary-600 dark:from-primary-500 dark:to-primary-600' : 'bg-gradient-to-b from-primary-400 to-primary-500 dark:from-primary-600 dark:to-primary-700'}` : 'text-slate-700 dark:text-neutral-300'} ${!selected && active ? 'bg-slate-100 dark:bg-neutral-700' : ''}`}
                    >
                        <span>{name}</span>
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
import { setLanguage } from '../api';
import { Icon } from '@steeze-ui/svelte-icon';
import { CheckCircle } from '@steeze-ui/heroicons';
import { t } from 'svelte-i18n';

export let locales = [];

let lang = document.documentElement
    .getAttribute('lang')
    .replace('-', '_');
let initialLang = lang;

// Convert locale map to iterable array
$: localeList = Object.keys(locales).map(code => {
    return {
        code: code,
        name: locales[code],
    };
});

$: if (lang != initialLang) {
    setLanguage(lang);
    console.log('setting language to ' + lang);
}
</script>
