const csrfToken = document.querySelector('[name="csrf-token"]').getAttribute('content')

const queryParams = () => {
    const parts = location.search.substring(1).split("&").filter(Boolean);
    const params = {};
    parts.forEach(p => {
        const split = p.split('=');
        params[split[0]] = split[1];
    });
    return params;
}

const paramsToQuery = (params) => {
    const parts = [];
    Object.keys(params).forEach(key => {
        const val = params[key] === null ? '' : params[key];
        parts.push(`${key}=${val}`);
    });
    return `?${parts.join('&')}`;
}

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

export const setTheme = value => {
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

export const setLanguage = value => {
    const params = queryParams();
    params.lang = value;
    location = paramsToQuery(params);
};

export const setSource = value => {
    const params = queryParams();
    params.source = value;
    location = paramsToQuery(params);
};

export const setSort = value => {
    const params = queryParams();
    params.sort = value;
    location = paramsToQuery(params);
}
