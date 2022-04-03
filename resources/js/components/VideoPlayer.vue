<template>
    <div
        class="video-player relative pb-9/16 select-none"
        @mouseenter="mouseover = true"
        @mouseleave="mouseover = false"
    >
        <!-- TODO: add mousemove handler with timeout -->
        <video
            class="absolute inset-0"
            playsinline
            ref="video"
            :src="src"
            :controls="false"
            :autoplay="autoplay"
            :poster="poster"
            @play="playing = true"
            @pause="playing = false"
            @click="playPause"
            @volumechange="volume = video.volume; muted = video.muted"
            @timeupdate="time = video.currentTime"
            @canplay="duration = video.duration"
            @durationchange="duration = video.duration"
        />

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform opacity-0"
            enter-to-class="transform opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform opacity-100"
            leave-to-class="transform opacity-0"
        >
            <div
                class="absolute inset-0 flex flex-col"
                v-show="mouseover || !playing"
            >
                <div class="flex items-center justify-center flex-1 bg-black/10" @click="playPause" @dblclick="fullscreen">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-32 w-32 opacity-80 hover:opacity-100"
                        viewBox="0 0 20 20"
                        fill="none"
                        v-show="!playing"
                    >
                        <circle cx="10" cy="10" r="6" fill="rgb(30, 41, 59)" />
                        <path v-else fill="#fff" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex gap-1 items-center px-2 text-white bg-gray-700/80 dark:bg-trueGray-700/80 backdrop-blur">
                    <button
                        type="button"
                        class="cursor-pointer p-2"
                        @click="playPause"
                    >
                        <component
                            :is="playing ? 'PauseIcon' : 'PlayIcon'"
                            class="w-5 h-5 text-white"
                        />
                    </button>

                    <input
                        class="flex-1"
                        type="range"
                        :min="0"
                        :max="duration"
                        step="any"
                        v-model="timeComputed"
                        :aria-label="$t('Time')"
                    />

                    <div class="text-sm leading-none ml-3">
                        {{ formatTime(time) }}
                        /
                        <span class="text-gray-200 dark:text-trueGray-200">{{ formatTime(duration) }}</span>
                    </div>

                    <button
                        type="button"
                        class="cursor-pointer p-2"
                        @click="video.muted = !video.muted"
                    >
                        <component
                            :is="muted ? 'VolumeOffIcon' : 'VolumeUpIcon'"
                            class="w-5 h-5 text-white"
                        />
                    </button>
                    <input
                        class="w-24"
                        type="range"
                        :min="0"
                        :max="1"
                        step="any"
                        v-model="volumeComputed"
                        :disabled="muted"
                        :aria-label="$t('Volume')"
                    />
                    <button
                        type="button"
                        class="cursor-pointer p-2"
                        @click="fullscreen"
                    >
                        <ArrowsExpandIcon class="w-5 h-5 text-white" />
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import { computed, ref, toRefs } from 'vue';
import {
    PauseIcon,
    PlayIcon,
    VolumeOffIcon,
    VolumeUpIcon,
    ArrowsExpandIcon,
} from '@heroicons/vue/solid';

export default {
    components: {
        PauseIcon,
        PlayIcon,
        VolumeOffIcon,
        VolumeUpIcon,
        ArrowsExpandIcon,
    },
    props: {
        src: {
            required: true,
            type: [String, Array],
        },
        poster: {
            type: String,
        },
        autoplay: {
            type: Boolean,
            default: false,
        },
    },
    setup(props) {
        const { autoplay, poster } = toRefs(props);

        const video = ref(null);
        const srcIndex = ref(0);
        const mouseover = ref(false);
        const playing = ref(false);
        const time = ref(0);
        const duration = ref(0);
        const volume = ref(0.6);
        const muted = ref(false);

        const src = computed(() => {
            if (typeof props.src === 'string') {
                return props.src;
            }
            return props.src[srcIndex.value];
        });

        const timeComputed = computed({
            get: () => {
                return time.value;
            },
            set: (e) => {
                video.value.currentTime = e;
                time.value = e;
            }
        });
        const volumeComputed = computed({
            get: () => {
                return volume.value;
            },
            set: (e) => {
                video.value.volume = e;
                volume.value = e;
            }
        });

        const playPause = () => {
            if (playing.value) {
                video.value.pause();
            } else {
                video.value.play();
            }
        };
        const formatTime = (time) => {
            let minutes = Math.floor(time / 60);
            let seconds = Math.floor(time - minutes * 60);
            seconds = String(seconds).padStart(2,0);
            return `${minutes}:${seconds}`;
        };
        const fullscreen = () => {
            if (document.fullscreenElement) {
                document.exitFullscreen();
            } else {
                video.value.parentElement.requestFullscreen();
            }
        };

        return {
            autoplay,
            poster,
            video,

            src,
            timeComputed,
            volumeComputed,

            mouseover,
            playing,
            time,
            duration,
            volume,
            muted,

            formatTime,
            playPause,
            fullscreen,
        };
    }
};
</script>
