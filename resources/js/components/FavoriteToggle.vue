<template>
    <Switch
        v-model="isFavorite"
        class="p-2 rounded-full text-sm font-medium text-red-600 focus:bg-gray-200 dark:focus:bg-trueGray-800 dark:text-red-500 hover:bg-gray-300 dark:hover:bg-trueGray-700 tooltip-right sm:tooltip-center focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-trueGray-900 dark:focus:ring-red-600"
        data-tooltip
        :aria-label="isFavorite ? removeLabel : addLabel"
    >
        <HeartIconSolid class="w-6 h-6" v-if="isFavorite" aria-hidden="true" />
        <HeartIcon class="w-6 h-6" v-else aria-hidden="true" />
    </Switch>
</template>

<script>
import { ref, watch } from "vue";
import { Switch } from "@headlessui/vue";
import { setFavorite } from "../api";
import { HeartIcon } from '@heroicons/vue/outline';
import { HeartIcon as HeartIconSolid } from '@heroicons/vue/solid';

export default {
    components: {
        Switch,
        HeartIcon,
        HeartIconSolid,
    },
    props: {
        uuid: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            default: 'video',
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
        const { uuid, addLabel, removeLabel, type } = props;
        const isFavorite = ref(props.isFavorite);

        watch(isFavorite, val => setFavorite(uuid, val, type));

        return { isFavorite, addLabel, removeLabel };
    },
};
</script>
