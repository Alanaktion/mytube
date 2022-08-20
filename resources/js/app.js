import './keybindings.js'

import { createApp } from 'vue'
import { i18nVue } from 'laravel-vue-i18n'
import NavMenu from './components/NavMenu.vue'
import UserMenu from './components/UserMenu.vue'
import LangMenu from './components/LangMenu.vue'
import ThemeMenu from './components/ThemeMenu.vue'
import FavoriteToggle from './components/FavoriteToggle.vue'
import DeleteChannel from './components/DeleteChannel.vue'
import DeletePlaylist from './components/DeletePlaylist.vue'
import DeleteVideo from './components/DeleteVideo.vue'
import FilterDialog from './components/FilterDialog.vue'
import SortBy from './components/SortBy.vue'
import SourceFilter from './components/SourceFilter.vue'
import DownloadMenu from './components/DownloadMenu.vue'
import JobDetails from './components/JobDetails.vue'
import ChannelRefresh from './components/ChannelRefresh.vue'
import ImportForm from './components/ImportForm.vue'

const langResolve = async lang => {
    const langs = import.meta.glob('../../lang/*.json');
    return await langs[`../../lang/${lang}.json`]();
}

if (document.querySelector('#app-nav')) {
    createApp({
        components: {
            NavMenu,
            UserMenu,
        }
    }).use(i18nVue, {
        resolve: langResolve,
    }).mount('#app-nav')
}

if (document.querySelector('#app-footer')) {
    createApp({
        components: {
            LangMenu,
            ThemeMenu,
        },
    }).use(i18nVue, {
        resolve: langResolve,
    }).mount('#app-footer')
}

if (document.querySelector('#app-favorite-toggle')) {
    createApp({
        components: {
            FavoriteToggle,
        },
    }).use(i18nVue, {
        resolve: langResolve,
    }).mount('#app-favorite-toggle')
}

if (document.querySelector('#app-sort-filter')) {
    createApp({
        components: {
            FilterDialog,
            SortBy,
            SourceFilter,
        },
    }).use(i18nVue, {
        resolve: langResolve,
    }).mount('#app-sort-filter')
}

if (document.querySelector('#app-download')) {
    createApp({
        components: {
            DownloadMenu,
        },
    }).use(i18nVue, {
        resolve: langResolve,
    }).mount('#app-download')
}

if (document.querySelector('#app-job-details')) {
    createApp({
        components: {
            JobDetails,
        },
    }).use(i18nVue, {
        resolve: langResolve,
    }).mount('#app-job-details')
}

if (document.querySelector('#app-channel-refresh')) {
    createApp({
        components: {
            ChannelRefresh,
            DeleteChannel,
            DeletePlaylist,
            DeleteVideo,
        },
    }).use(i18nVue, {
        resolve: langResolve,
    }).mount('#app-channel-refresh')
}

if (document.querySelector('#app-admin-import')) {
    createApp({
        components: {
            ImportForm,
        },
    }).use(i18nVue, {
        resolve: langResolve,
    }).mount('#app-admin-import')
}
