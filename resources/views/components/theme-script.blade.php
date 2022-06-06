<script>
    // Set color scheme dynamically
    const media = window.matchMedia('(prefers-color-scheme: dark)')
    const docCL = document.documentElement.classList
    const themeColor = document.querySelector('meta[name="theme-color"]')
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && media.matches)) {
        docCL.add('dark')
        themeColor.setAttribute('content', 'rgb(38, 38, 38)')
    } else {
        docCL.remove('dark')
    }
    media.addEventListener('change', () => {
        if ('theme' in localStorage) {
            return
        }
        if (media.matches) {
            docCL.add('dark')
            themeColor.setAttribute('content', 'rgb(38, 38, 38)')
        } else {
            docCL.remove('dark')
            themeColor.setAttribute('content', 'rgb(30, 41, 59)')
        }
    })
</script>
