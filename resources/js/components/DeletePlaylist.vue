<template>
    <button class="btn btn-secondary rounded-full hover:text-red-500 dark:hover:text-red-300" @click="open = true">
        {{ $t('Delete') }}
    </button>

    <AppDialog :title="$t('Delete playlist')" :open="open" @close="open = false">
        <form :action="`/playlists/${uuid}`" method="post">
            <input type="hidden" name="_token" :value="csrfToken">
            <input type="hidden" name="_method" value="DELETE">

            <p class="text-sm text-slate-500 dark:text-neutral-400 mb-6">
                This will delete the playlist and related metadata.
            </p>

            <div class="flex items-start mb-3">
                <div class="flex items-center h-5">
                    <input type="checkbox" class="input-checkbox" id="videos" name="videos" v-model="videos" value="1">
                </div>
                <div class="ml-3 text-sm leading-5">
                    <label for="videos" class="font-semibold text-slate-700 dark:text-neutral-300">
                        {{ $t('Videos') }}
                    </label>
                    <p class="text-slate-500 dark:text-neutral-400">
                        Delete all videos in the playlist.
                    </p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input type="checkbox" class="input-checkbox" id="files" name="files" v-model="files" value="1">
                </div>
                <div class="ml-3 text-sm leading-5">
                    <label for="files" class="font-semibold text-slate-700 dark:text-neutral-300">
                        {{ $t('Files') }}
                    </label>
                    <p class="text-slate-500 dark:text-neutral-400">
                        Delete local files for the videos in the playlist.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="btn btn-destructive">
                    Delete
                </button>
            </div>
        </form>
    </AppDialog>
</template>

<script setup>
import { ref } from 'vue';
import AppDialog from './AppDialog.vue';

const props = defineProps({
    uuid: {
        type: String,
        required: true,
    },
});

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const open = ref(false);
const videos = ref(false);
const files = ref(false);
</script>
