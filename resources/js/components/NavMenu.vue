<template>
    <button
        type="button"
        class="md:hidden absolute top-1 right-3 px-3 py-2 rounded-md text-sm font-medium focus:text-white focus:bg-slate-700 dark:focus:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-800 focus:ring-primary-500 dark:focus:ring-offset-neutral-800 dark:focus:ring-primary-600"
        :class="{
            'text-slate-300 dark:text-neutral-300 hover:text-white hover:bg-slate-700 dark:hover:bg-neutral-700': !open,
            'text-white bg-slate-700 dark:bg-neutral-700': open,
        }"
        :aria-expanded="open ? 'true' : 'false'"
        aria-controls="nav-menu"
        @click="open = !open"
    >
        <span class="sr-only">{{ label }}</span>
        <MenuIcon class="w-6 h-6" aria-hidden="true" />
    </button>

    <div id="nav-menu" class="md:flex flex-1" v-show="visible">
        <slot></slot>
    </div>
</template>

<script>
import { ref, onBeforeUnmount, computed } from 'vue';
import { MenuIcon } from '@heroicons/vue/outline';

export default {
    components: {
        MenuIcon,
    },
    props: {
        label: {
            type: String,
            default: 'Menu',
        },
    },
    setup(props) {
        const { label } = props;
        const open = ref(false);
        const alwaysShow = ref(false);

        // Handle viewport sizing
        const onResize = () => {
            alwaysShow.value = window.innerWidth >= 768;
        };
        onResize();
        window.addEventListener('resize', onResize);
        onBeforeUnmount(() => window.removeEventListener('resize', onResize));

        // Determine final visibility
        const visible = computed(() => open.value || alwaysShow.value);

        return { label, open, visible };
    }
};
</script>
