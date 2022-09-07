<Switch
    checked={isFavorite}
    on:change={(e) => (isFavorite = e.detail)}
    class={`btn btn-secondary p-2 rounded-full tooltip-left ${isFavorite ? 'text-pink-600 dark:text-pink-500' : ''}`}
    data-tooltip
    aria-label={isFavorite ? $t('Remove from Favorites') : $t('Add to Favorites')}
>
    <Icon src={Heart} theme={isFavorite ? 'solid' : 'default'} class="w-5 h-5" aria-hidden="true" />
</Switch>

<script>
import { Switch } from "@rgossiaux/svelte-headlessui";
import { setFavorite } from "../api";
import { Icon } from '@steeze-ui/svelte-icon';
import { Heart } from '@steeze-ui/heroicons';
import { t } from 'svelte-i18n';

export let uuid;
export let type = 'video';
export let isFavorite = false;

let prevIsFavorite = isFavorite;

$: if (prevIsFavorite !== isFavorite) {
    setFavorite(uuid, isFavorite, type);
    prevIsFavorite = isFavorite;
}
</script>
