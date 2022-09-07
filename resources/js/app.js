import './keybindings.js'
import './i18n.js'

import NavMenu from './components/NavMenu.svelte'
import UserMenu from './components/UserMenu.svelte'
import LangMenu from './components/LangMenu.svelte'
import ThemeMenu from './components/ThemeMenu.svelte'
import FavoriteToggle from './components/FavoriteToggle.svelte'
import DeleteChannel from './components/DeleteChannel.svelte'
import DeletePlaylist from './components/DeletePlaylist.svelte'
import DeleteVideo from './components/DeleteVideo.svelte'
import FilterDialog from './components/FilterDialog.svelte'
import SortBy from './components/SortBy.svelte'
import SourceFilter from './components/SourceFilter.svelte'
import DownloadMenu from './components/DownloadMenu.svelte'
import JobDetails from './components/JobDetails.svelte'
import ChannelRefresh from './components/ChannelRefresh.svelte'
import ImportForm from './components/ImportForm.svelte'

new NavMenu({
    target: document.querySelector('#app-nav'),
    props: {
        label: document.querySelector('#app-nav > nav-menu').getAttribute('label'),
    },
})

const menuElement = document.querySelector('user-menu')
new UserMenu({
    target: menuElement,
    props: {
        name: menuElement.getAttribute('name'),
        token: menuElement.getAttribute('token'),
        admin: menuElement.hasAttribute('admin'),
    },
})

const langMenuElement = document.querySelector('#app-footer lang-menu')
new LangMenu({
    target: langMenuElement,
    props: {
        locales: JSON.parse(langMenuElement.getAttribute('locales')),
    },
})
new ThemeMenu({
    target: document.querySelector('#app-footer theme-menu'),
})

const favoriteToggleElement = document.querySelector('#app-favorite-toggle favorite-toggle')
if (favoriteToggleElement) {
    new FavoriteToggle({
        target: favoriteToggleElement,
        props: {
            type: favoriteToggleElement.getAttribute('type'),
            uuid: favoriteToggleElement.getAttribute('uuid'),
            isFavorite: favoriteToggleElement.hasAttribute('is-favorite'),
        },
    })
}

const filterDialogElement = document.querySelector('#app-sort-filter filter-dialog')
if (filterDialogElement) {
    new FilterDialog({
        target: filterDialogElement,
        props: {
            sources: JSON.parse(filterDialogElement.getAttribute('sources')),
            params: JSON.parse(filterDialogElement.getAttribute('params')),
        },
    })
}

const sortByElement = document.querySelector('#app-sort-filter sort-by')
if (sortByElement) {
    new SortBy({
        target: sortByElement,
        props: {
            value: sortByElement.getAttribute('value'),
        },
    })
}

const sourceFilterElement = document.querySelector('#app-sort-filter source-filter')
if (sourceFilterElement) {
    new SourceFilter({
        target: sourceFilterElement,
        props: {
            sources: JSON.parse(filterDialogElement.getAttribute('sources')),
            value: sourceFilterElement.getAttribute('value'),
        },
    })
}

const downloadMenuElement = document.querySelector('#app-download download-menu')
if (downloadMenuElement) {
    new DownloadMenu({
        target: downloadMenuElement,
        props: {
            files: JSON.parse(downloadMenuElement.getAttribute('files')),
        },
    })
}

const jobDetailsElement = document.querySelector('#app-job-details job-details')
if (jobDetailsElement) {
    new JobDetails({
        target: jobDetailsElement,
        props: {
            type: jobDetailsElement.getAttribute('type'),
            id: jobDetailsElement.getAttribute('id'),
        },
    })
}

const channelRefreshElement = document.querySelector('#app-channel-refresh channel-refresh')
if (channelRefreshElement) {
    new ChannelRefresh({
        target: channelRefreshElement,
        props: {
            uuid: channelRefreshElement.getAttribute('uuid'),
        },
    })
}

const deleteChannelElement = document.querySelector('#app-channel-refresh delete-channel')
if (deleteChannelElement) {
    new DeleteChannel({
        target: deleteChannelElement,
        props: {
            uuid: deleteChannelElement.getAttribute('uuid'),
        },
    })
}

const deletePlaylistElement = document.querySelector('#app-channel-refresh delete-playlist')
if (deletePlaylistElement) {
    new DeletePlaylist({
        target: deletePlaylistElement,
        props: {
            uuid: deletePlaylistElement.getAttribute('uuid'),
        },
    })
}

const deleteVideoElement = document.querySelector('#app-channel-refresh delete-video')
if (deleteVideoElement) {
    new DeleteVideo({
        target: deleteVideoElement,
        props: {
            uuid: deleteVideoElement.getAttribute('uuid'),
        },
    })
}

const importFormElement = document.querySelector('#app-admin-import import-form')
if (importFormElement) {
    new ImportForm({
        target: importFormElement,
    })
}
