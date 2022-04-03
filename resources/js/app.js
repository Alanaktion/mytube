import './keybindings.js'

import { createApp } from 'vue'
import { i18nVue } from 'laravel-vue-i18n'
import NavMenu from './components/NavMenu.vue'
import FavoriteToggle from './components/FavoriteToggle.vue'
import LangMenu from './components/LangMenu.vue'
import ThemeMenu from './components/ThemeMenu.vue'
import SortBy from './components/SortBy.vue'
import SourceFilter from './components/SourceFilter.vue'
import UserMenu from './components/UserMenu.vue'
import VideoPlayer from './components/VideoPlayer.vue'

createApp({
    components: {
        NavMenu,
        FavoriteToggle,
        LangMenu,
        ThemeMenu,
        SortBy,
        SourceFilter,
        UserMenu,
        VideoPlayer,
    },
}).use(i18nVue, {
    resolve: lang => import(`../../lang/${lang}.json`),
}).mount('#app')
