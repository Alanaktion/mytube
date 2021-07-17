import mousetrap from 'mousetrap'

// Global key bindings
mousetrap.bind('g h', () => {
    location = '/';
});
mousetrap.bind('g v', () => {
    location = '/videos';
});
mousetrap.bind('g p', () => {
    location = '/playlists';
});
mousetrap.bind('g c', () => {
    location = '/channels';
});
mousetrap.bind('g f', () => {
    location = '/favorites';
});
mousetrap.bind('g a', () => {
    location = '/admin';
});
mousetrap.bind(['/', 'command+k', 'ctrl+k'], () => {
    const el = document.querySelector('input[type=search]');
    el.focus();
    el.select();
    return false;
});

// Pagination key bindings
mousetrap.bind('right', () => {
    location = document.querySelector('a[rel="next"]').getAttribute('href');
});
mousetrap.bind('left', () => {
    location = document.querySelector('a[rel="prev"]').getAttribute('href');
});

// Video page key bindings
if (location.pathname.match(/^\/videos\/[0-9a-z_-]+/i)) {
    // Play/pause
    mousetrap.bind(['k', 'space'], () => {
        const el = document.querySelector('video');
        if (el.paused) {
            el.play();
        } else {
            el.pause();
        }
    });

    // Seek backward
    mousetrap.bind('j', () => {
        const el = document.querySelector('video');
        if (!el.seekable) {
            return;
        }
        if (el.currentTime - 10 < 0) {
            el.currentTime = 0;
        } else {
            el.currentTime -= 10;
        }
    });

    // Seek forward
    mousetrap.bind('l', () => {
        const el = document.querySelector('video');
        if (!el.seekable) {
            return;
        }
        if (el.duration && el.currentTime - 10 > el.duration) {
            el.currentTime = el.duration;
            el.pause();
        } else {
            el.currentTime += 10;
        }
    });

    // Toggle mute
    mousetrap.bind('m', () => {
        const el = document.querySelector('video');
        el.muted = !el.muted;
    });

    // Toggle fullscreen
    mousetrap.bind('f', () => {
        if (document.fullscreenEnabled) {
            document.exitFullscreen();
        } else {
            const el = document.querySelector('video');
            el.requestFullscreen();
        }
    });
}
