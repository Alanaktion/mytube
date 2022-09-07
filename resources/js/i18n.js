import { addMessages, register, init, getLocaleFromNavigator } from 'svelte-i18n'

addMessages('en', import('../../lang/en.json'))
register('en-GB', () => import('../../lang/en_GB.json'))
register('es', () => import('../../lang/es.json'))
register('ja', () => import('../../lang/ja.json'))
register('ru', () => import('../../lang/ru.json'))
register('tok', () => import('../../lang/tok.json'))

init({
    fallbackLocale: 'en',
    initialLocale: getLocaleFromNavigator(),
})

// TODO: get $t() calls to actually return translated values. Right now they return the key.
