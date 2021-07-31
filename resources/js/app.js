import './keybindings.js'

import { createApp } from 'vue'
import NavMenu from './components/NavMenu.vue'
import FavoriteToggle from './components/FavoriteToggle.vue'
import LangMenu from './components/LangMenu.vue'
import ThemeMenu from './components/ThemeMenu.vue'
import SortBy from './components/SortBy.vue'
import SourceFilter from './components/SourceFilter.vue'
import UserMenu from './components/UserMenu.vue'

createApp({
    components: {
        NavMenu,
        FavoriteToggle,
        LangMenu,
        ThemeMenu,
        SortBy,
        SourceFilter,
        UserMenu,
    },
}).mount('#app')
