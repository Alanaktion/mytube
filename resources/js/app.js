import './keybindings.js'

import { createApp } from 'vue'
import NavMenu from './components/NavMenu.vue'
import FavoriteToggle from './components/FavoriteToggle.vue'
import LangMenu from './components/LangMenu.vue'
import ThemeMenu from './components/ThemeMenu.vue'

createApp({
    components: {
        NavMenu,
        FavoriteToggle,
        LangMenu,
        ThemeMenu,
    },
}).mount('#app')
