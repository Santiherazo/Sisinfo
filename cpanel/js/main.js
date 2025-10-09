lucide.createIcons();

let sidebarOpen = false;
let userMenuOpen = false;

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    sidebarOpen = !sidebarOpen;
    
    if (sidebarOpen) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

function toggleUserMenu() {
    const userMenu = document.getElementById('user-menu');
    userMenuOpen = !userMenuOpen;
    
    if (userMenuOpen) {
        userMenu.classList.remove('hidden');
    } else {
        userMenu.classList.add('hidden');
    }
}

document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('user-menu');
    const userMenuButton = event.target.closest('[onclick="toggleUserMenu()"]');
    
    if (!userMenuButton && !userMenu.contains(event.target) && userMenuOpen) {
        toggleUserMenu();
    }
});

window.addEventListener('resize', function() {
    if (window.innerWidth >= 1024) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('hidden');
        sidebarOpen = false;
    }
});