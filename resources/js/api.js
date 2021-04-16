const csrfToken = document.querySelector('[name="csrf-token"]').getAttribute('content')

export const setFavorite = async (uuid, value, type='video') => {
    fetch(`/favorites/toggle${type.charAt(0).toUpperCase() + type.slice(1)}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken,
        },
        body: JSON.stringify({
            uuid,
            value,
        }),
    });
};

export const setTheme = (value) => {
    const docCL = document.documentElement.classList;
    let theme;
    if (value === 'auto') {
        localStorage.removeItem('theme');
        theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    } else {
        localStorage.theme = value;
        theme = value;
    }
    docCL.add(theme);
    docCL.remove(theme === 'light' ? 'dark' : 'light');
};
