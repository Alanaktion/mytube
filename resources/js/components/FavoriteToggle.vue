<template>
    <Switch
        v-model="isFavorite"
        class="p-2 rounded-full text-sm font-medium text-red-600 focus:bg-gray-200 dark:focus:bg-trueGray-800 dark:text-red-500 hover:bg-gray-300 dark:hover:bg-trueGray-700 tooltip-right sm:tooltip-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-trueGray-900 dark:focus:ring-red-600"
        data-tooltip
        :aria-label="isFavorite ? removeLabel : addLabel"
    >
        <svg
            class="w-6 h-6"
            :fill="isFavorite ? 'currentColor' : 'none'"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            />
        </svg>
    </Switch>
</template>

<script>
import { ref, watch } from "vue";
import { Switch } from "@headlessui/vue";
import { setFavorite } from "../api";

export default {
    components: { Switch },
    props: {
        uuid: {
            type: String,
            required: true,
        },
        isFavorite: {
            type: Boolean,
            default: false,
        },
        addLabel: {
            type: String,
            default: 'Add to Favorites',
        },
        removeLabel: {
            type: String,
            default: 'Remove from Favorites',
        },
    },
    setup(props) {
        const { uuid, addLabel, removeLabel } = props;
        const isFavorite = ref(props.isFavorite);

        watch(isFavorite, val => setFavorite(uuid, val));

        return { isFavorite, addLabel, removeLabel };
    },
};
</script>
