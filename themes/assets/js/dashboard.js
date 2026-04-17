// Toggle Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebar');

    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
    toggleBtn.classList.toggle('collapsed');

    // Change arrow direction
    const arrow = toggleBtn.querySelector('i');
    if (sidebar.classList.contains('collapsed')) {
        arrow.classList.remove('fa-chevron-left');
        arrow.classList.add('fa-chevron-right');
    } else {
        arrow.classList.remove('fa-chevron-right');
        arrow.classList.add('fa-chevron-left');
    }
}

// Toggle Dark Mode
function toggleDarkMode() {
    const body = document.body;
    const themeIcon = document.querySelector('.theme-toggle i');

    if (body.classList.contains('dark-mode')) {
        body.classList.remove('dark-mode');
        themeIcon.classList.remove('fa-sun');
        themeIcon.classList.add('fa-moon');
        document.cookie = "dark_mode=light; path=/; max-age=" + 60 * 60 * 24 * 365;
    } else {
        body.classList.add('dark-mode');
        themeIcon.classList.remove('fa-moon');
        themeIcon.classList.add('fa-sun');
        document.cookie = "dark_mode=dark; path=/; max-age=" + 60 * 60 * 24 * 365;
    }
}

// Toggle Submenu
function toggleSubmenu(element) {
    element.classList.toggle('open');
    const submenu = element.nextElementSibling;

    if (submenu.style.display === 'block') {
        submenu.style.display = 'none';
    } else {
        submenu.style.display = 'block';
    }
}

// Initialize submenus (closed by default)
document.addEventListener('DOMContentLoaded', function () {
    const submenuTriggers = document.querySelectorAll('.nav-item.has-submenu');
    submenuTriggers.forEach(trigger => {
        const submenu = trigger.nextElementSibling;
        if (!submenu || !submenu.classList.contains('submenu')) {
            return;
        }

        submenu.style.display = trigger.classList.contains('open') ? 'block' : 'none';
    });
});

// Handle responsive behavior
function handleResize() {
    if (window.innerWidth <= 768) {
        document.getElementById('sidebar').classList.add('collapsed');
        document.getElementById('mainContent').classList.add('expanded');
    }
}

window.addEventListener('resize', handleResize);
handleResize();