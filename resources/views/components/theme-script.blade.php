<script>
    // Set color scheme dynamically
    const media = window.matchMedia('(prefers-color-scheme: dark)')
    const docCL = document.documentElement.classList
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && media.matches)) {
        docCL.add('dark')
    } else {
        docCL.remove('dark')
    }
    media.addEventListener('change', () => {
        if ('theme' in localStorage) {
            return
        }
        if (media.matches) {
            docCL.add('dark')
        } else {
            docCL.remove('dark')
        }
    })
</script>
