<button
    type="button"
    class="{`md:hidden absolute top-1 right-3 px-3 py-2 rounded-md text-sm font-medium focus:text-white focus:bg-slate-700 dark:focus:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 focus:ring-primary-500 dark:focus:ring-offset-neutral-800 dark:focus:ring-primary-600 ${open ? 'text-white bg-slate-700 dark:bg-neutral-700' : 'text-slate-300 dark:text-neutral-300 hover:text-white hover:bg-slate-700 dark:hover:bg-neutral-700'}`}"
    aria-expanded={open ? 'true' : 'false'}
    aria-controls="nav-menu"
    on:click={() => (open = !open)}
>
    <span class="sr-only">{label}</span>
    <Icon src={Bars3} theme="default" class="w-6 h-6" aria-hidden="true" />
</button>

<!-- TODO: Currently the nav menu is not actually inserted into the slot here, so this component is not functional. -->
{#if visible}
<div id="nav-menu" class="md:flex flex-1">
    <slot />
</div>
{/if}

<script setup>
import { Icon } from '@steeze-ui/svelte-icon';
import { Bars3 } from '@steeze-ui/heroicons';

export let label = 'Menu';

let open = false;
let alwaysShow = false;

// Handle viewport sizing
const onResize = () => {
    alwaysShow = window.innerWidth >= 768;
};
onResize();
window.addEventListener('resize', onResize);

// Determine final visibility
$: visible = open || alwaysShow;
</script>
