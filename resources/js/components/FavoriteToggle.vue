<template>
    <Switch
        v-model="isFavorite"
        class="btn btn-secondary p-2 rounded-full tooltip-left"
        :class="{
            'text-pink-600 dark:text-pink-500': isFavorite,
        }"
        data-tooltip
        :aria-label="isFavorite ? $t('Remove from Favorites') : $t('Add to Favorites')"
    >
        <HeartIconSolid class="w-5 h-5" v-if="isFavorite" aria-hidden="true" />
        <HeartIcon class="w-5 h-5" v-else aria-hidden="true" />
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
    },
    setup(props) {
        const { uuid, type } = props;
        const isFavorite = ref(props.isFavorite);

        watch(isFavorite, val => setFavorite(uuid, val, type));

        return { isFavorite };
    },
};
</script>
