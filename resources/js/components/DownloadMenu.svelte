<Menu class="relative self-center mb-4 sm:mb-0" let:open>
    <MenuButton class={`btn btn-secondary rounded-full ${open && 'btn-secondary-active'}`}>
        <span class="md:sr-only lg:not-sr-only">{$t('Download')}</span>
        <Icon src={ArrowDownTray} class="md:hidden lg:block w-5 h-5 ml-auto sm:ml-2 -mr-1" aria-hidden="true" />
    </MenuButton>

    <Transition
        class="z-30"
        enter="transition duration-100 ease-out"
        enterFrom="scale-95 opacity-0"
        enterTo="scale-100 opacity-100"
        leave="transition duration-75 ease-in"
        leaveFrom="scale-100 opacity-100"
        leaveTo="scale-95 opacity-0"
    >
        <MenuItems
            class="origin-top-right absolute -left-2 sm:left-auto sm:-right-2 min-w-40 p-2 mt-2 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
        >
            {#each files as file}
            <MenuItem let:active>
                <a
                    href={file.url}
                    class={`flex justify-between gap-4 items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md whitespace-nowrap ${active ? 'bg-gradient-to-b from-primary-500 to-primary-600 text-white text-shadow-px-primary' : 'text-slate-700 dark:text-neutral-300'}`}
                    download
                >
                    {#if file.height}
                    <span>
                        {file.height}p {file.mime_type.substring(6).toUpperCase()}
                    </span>
                    {/if}
                    <span v-else>
                        {formatType(file.mime_type)}
                    </span>
                    {#if file.size}
                    <span class={active ? 'text-slate-100' : 'text-slate-500 dark:text-neutral-500'}>
                        {formatSize(file.size)}
                    </span>
                    {/if}
                </a>
            </MenuItem>
            {/each}
        </MenuItems>
    </Transition>
</Menu>

<script>
import { Menu, MenuButton, MenuItems, MenuItem, Transition } from '@rgossiaux/svelte-headlessui';
import { Icon } from '@steeze-ui/svelte-icon';
import { ArrowDownTray } from '@steeze-ui/heroicons';
import { t } from 'svelte-i18n';

export let files = [];

const formatSize = (size) => {
    if (size > 1e9) {
        return `${(size / 1e9).toFixed(2)} GB`;
    }
    if (size > 1e6) {
        return `${(size / 1e6).toFixed(2)} MB`;
    }
    return `${(size / 1e3).toFixed(2)} KB`;
}

const formatType = mime_type => {
    const type = mime_type.split('/')[1];
    return {
        'x-matroska': 'MKV'
    }[type] || type.toUpperCase();
}
</script>
