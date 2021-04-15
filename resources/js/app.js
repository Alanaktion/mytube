import './keybindings.js'

import { createApp } from 'vue'
import FavoriteToggle from './components/FavoriteToggle.vue'
import ThemeMenu from './components/ThemeMenu.vue'

createApp({
    components: {
        FavoriteToggle,
        ThemeMenu,
    },
}).mount('#app')
