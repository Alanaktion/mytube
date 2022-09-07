<Menu let:open>
    <div>
        <MenuButton
            class="{`flex px-3 py-2 w-full sm:w-auto rounded-md text-sm font-medium focus:text-white focus:bg-slate-700 dark:focus:bg-neutral-700 text-slate-300 dark:text-neutral-300 hover:text-white hover:bg-slate-700 dark:hover:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 focus:ring-primary-500 dark:focus:ring-offset-neutral-800 dark:focus:ring-primary-600 ${open ? 'bg-slate-700 dark:bg-neutral-700' : ''}`}"
        >
            <Icon src={UserCircle} theme="mini" class="hidden md:block lg:hidden w-5 h-5" aria-hidden="true" />
            <span class="md:sr-only lg:not-sr-only">{name}</span>
            <Icon src={ChevronDown} theme="mini" class="md:hidden lg:block w-5 h-5 ml-auto sm:ml-2 -mr-1" aria-hidden="true" />
        </MenuButton>
    </div>

    <form class="hidden" method="POST" action="/logout" aria-hidden="true" bind:this={form}>
        <input type="hidden" name="_token" bind:value={token}>
        <button type="submit"></button>
    </form>

    <Transition
        class="relative z-30"
        enter="transition duration-100 ease-out"
        enterFrom="scale-95 opacity-0"
        enterTo="scale-100 opacity-100"
        leave="transition duration-75 ease-in"
        leaveFrom="scale-100 opacity-100"
        leaveTo="scale-95 opacity-0"
    >
        <MenuItems
            class="origin-top-right absolute -left-2 sm:left-auto sm:-right-2 w-40 p-2 mt-2 flex flex-col gap-1 rounded-lg shadow-lg bg-white dark:bg-neutral-800 border dark:border-neutral-850 focus:outline-none"
        >
            <MenuItem let:active>
                <a
                    href="/users"
                    class="{`flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md ${active ? 'bg-gradient-to-b from-primary-500 to-primary-600 text-white text-shadow-px-primary' : 'text-slate-700 dark:text-neutral-300'}`}"
                >
                    {$t('Account')}
                </a>
            </MenuItem>
            {#if admin}
                <MenuItem let:active>
                    <a
                        href="/admin"
                        class="{`flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md ${active ? 'bg-gradient-to-b from-primary-500 to-primary-600 text-white text-shadow-px-primary' : 'text-slate-700 dark:text-neutral-300'}`}"
                    >
                        {$t('Administration')}
                    </a>
                </MenuItem>
            {/if}
            <MenuItem let:active>
                <button
                    on:click={logOut}
                    class="{`flex items-center appearance-none w-full px-4 py-2 text-sm cursor-pointer rounded-md ${active ? 'bg-gradient-to-b from-primary-500 to-primary-600 text-white text-shadow-px-primary' : 'text-slate-700 dark:text-neutral-300'}`}"
                >
                    {$t('Log out')}
                </button>
            </MenuItem>
        </MenuItems>
    </Transition>
</Menu>

<script>
import { Menu, MenuButton, MenuItems, MenuItem, Transition } from '@rgossiaux/svelte-headlessui';
import { Icon } from '@steeze-ui/svelte-icon';
import { ChevronDown, UserCircle } from '@steeze-ui/heroicons';
import { t } from 'svelte-i18n';

export let name = '';
export let token = null;
export let admin = false;

let form = null;
const logOut = () => {
    form && form.submit();
};
</script>
