(function () {
    var btn = document.getElementById('sh-dark-toggle');
    var sun = document.getElementById('sh-icon-sun');
    var moon = document.getElementById('sh-icon-moon');

    if (!btn || !sun || !moon) {
        return;
    }

    function updateThemeMeta(isDark) {
        var themeColor = document.querySelector('meta[name="theme-color"]');
        if (themeColor) {
            themeColor.content = isDark ? '#0f172a' : '#ffffff';
        }
    }

    function updateIcons() {
        var isDark = document.documentElement.classList.contains('dark');
        sun.style.display = isDark ? 'block' : 'none';
        moon.style.display = isDark ? 'none' : 'block';
        updateThemeMeta(isDark);
    }

    function toggleThemeClass() {
        document.documentElement.classList.toggle('dark');
        try {
            localStorage.setItem('sh-dark', document.documentElement.classList.contains('dark') ? 'true' : 'false');
        } catch (error) {
            // Ignore storage failures in private/sandboxed contexts.
        }
        updateIcons();
    }

    updateIcons();

    btn.addEventListener('click', function () {
        document.documentElement.classList.add('theme-switching');

        if (!document.startViewTransition) {
            toggleThemeClass();
            setTimeout(function () {
                document.documentElement.classList.remove('theme-switching');
            }, 60);
            return;
        }

        var transition = document.startViewTransition(toggleThemeClass);
        transition.finished.finally(function () {
            document.documentElement.classList.remove('theme-switching');
        });
    });
})();
