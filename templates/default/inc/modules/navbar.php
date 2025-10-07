<?php
if (!defined('NAVBAR_CACHE_DIR')) {
    define('NAVBAR_CACHE_DIR', __PATH_CACHE__ . '/navbar/');
}

if (!file_exists(NAVBAR_CACHE_DIR)) {
    mkdir(NAVBAR_CACHE_DIR, 0755, true);
}

$NavbarCache = new Cache();
$NavbarCache->configure('file', NAVBAR_CACHE_DIR)
            ->setCachePath(NAVBAR_CACHE_DIR, 'navbar');

$profileData = [
    'profileImg' => null,
    'fullName' => null,
    'email' => null,
    'initials' => '',
    'notifications' => [],
    'unreadCount' => 0,
    'hasAdminAccess' => false,
    'hasEvaluatorAccess' => false
];

if (isLoggedIn()) {
    $uid = $_SESSION['userid'];
    $cacheDuration = 300;

    try {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read_id'])) {
            $db = Connection::Database('sisinfo');
            $pdo = $db->getConnection();
            $logger = new ErrorLogger();
            $profileManager = new ProfileManager($pdo, $db, $logger);
            
            $notificationId = (int) $_POST['mark_read_id'];
            $result = $profileManager->markNotificationAsRead($notificationId);
            
            $NavbarCache->invalidateNavbarCache($uid);
            
            echo json_encode(['success' => $result]);
            exit;
        }

        $cachedProfile = $NavbarCache->getNavbarProfile($uid);
        
        if ($cachedProfile) {
            $profileData = $cachedProfile;
        } else {
            $db = Connection::Database('sisinfo');
            $pdo = $db->getConnection();
            $logger = new ErrorLogger();
            $profileManager = new ProfileManager($pdo, $db, $logger);
            $profile = $profileManager->getProfile($uid);

            if ($profile) {
                $nameParts = explode(' ', trim($profile['full_name']), 2);
                $initials = strtoupper(
                    substr($nameParts[0] ?? '', 0, 1) . 
                    substr($nameParts[1] ?? '', 0, 1)
                );

                $profileData = [
                    'profileImg' => $profile['avatar'],
                    'fullName' => $profile['full_name'],
                    'email' => $profile['email'],
                    'initials' => $initials,
                    'notifications' => $profile['unread_notifications'] ?? [],
                    'unreadCount' => count($profile['unread_notifications'] ?? []),
                    'hasAdminAccess' => accessManager()->canAccess($uid, ['administrador'], [['module' => 'Cpanel', 'action' => 'access']]),
                    'hasEvaluatorAccess' => accessManager()->canAccess($uid, ['evaluador'], [['module' => 'Epanel', 'action' => 'access']])
                ];

                $NavbarCache->storeNavbarProfile($uid, $profileData, $cacheDuration);
            }
        }
    } catch (Throwable $e) {
        error_log('[Navbar Error] ' . $e->getMessage());
    }
}
?>

<nav id="main-nav" class="fixed top-0 w-full z-50 bg-[var(--color-navbar-bg)] backdrop-blur-md border-b border-[var(--color-border)] transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="<?php echo __BASE_URL__; ?>" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <img src="<?php echo __PATH_TEMPLATE_IMG__ . 'logo.png'; ?>" alt="Logo" class="w-12 h-12">
                </div>
                <div class="hidden sm:block">
                    <span class="text-xl font-bold text-[var(--color-navbar-text)]"><?php config('website_name'); ?></span>
                    <p class="text-xs text-[var(--color-text-muted)]"><?php config('website_slogan'); ?></p>
                </div>
            </a>

            <div class="hidden md:flex items-center space-x-8">
                <?php templateBuildNavbar(); ?>
            </div>

            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-2 mr-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="themeToggle" class="sr-only peer">
                        <div class="w-14 h-7 bg-[var(--color-border-muted)] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-[var(--color-surface)] after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[var(--color-primary)] relative">
                            <div class="absolute inset-0 flex items-center justify-between px-1.5">
                                <svg class="w-4 h-4 text-[var(--color-warning)]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                </svg>
                                <svg class="w-4 h-4 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                            </div>
                        </div>
                    </label>
                </div>

                <?php if (isLoggedIn()) { ?>
                    <div class="relative group">
                        <button id="notificationBtn" class="relative p-2 rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-colors">
                            <svg class="w-5 h-5 text-[var(--color-navbar-text)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <?php if ($profileData['unreadCount'] > 0) { ?>
                                <span class="absolute -top-1 -right-1 bg-[var(--color-badge)] text-white rounded-full text-xs px-1.5 py-0.5 min-w-[18px] text-center"><?php echo $profileData['unreadCount']; ?></span>
                            <?php } ?>
                        </button>
                        
                        <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 hidden bg-[var(--color-dropdown-bg)] text-[var(--color-text)] border border-[var(--color-border)] rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                            <div class="flex justify-between items-center p-4 border-b border-[var(--color-border)] font-semibold">
                                <span class="text-[var(--color-heading)]">Notificaciones</span>
                                <a href="<?php echo __BASE_URL__; ?>notificaciones" class="text-xs text-[var(--color-link)] hover:underline">Ver todas</a>
                            </div>
                            <?php if (!empty($profileData['notifications'])) { ?>
                                <ul class="divide-y divide-[var(--color-border)]"> 
                                    <?php foreach ($profileData['notifications'] as $notif) { ?>
                                        <li class="p-3 hover:bg-[var(--color-dropdown-hover)] text-sm flex justify-between items-start transition-colors">
                                            <div class="flex-1">
                                                <?php echo htmlspecialchars($notif['message']); ?>
                                                <div class="text-xs text-[var(--color-text-muted)] mt-1"><?php echo date('d M H:i', strtotime($notif['created_at'])); ?></div>
                                            </div>
                                            <button onclick="marcarComoLeida(<?php echo $notif['id']; ?>, this)"
                                                    class="text-xs text-[var(--color-link)] hover:underline ml-2 whitespace-nowrap">
                                                Marcar
                                            </button>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                <div class="p-4 text-sm text-[var(--color-text-muted)] text-center">No tienes notificaciones nuevas.</div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="relative group">
                        <button id="userBtn" class="focus:outline-none">
                            <?php if (!empty($profileData['profileImg'])): ?>
                                <img id="userProfileImage" src="<?php echo htmlspecialchars(__PATH_TEMPLATE_IMG__ . $profileData['profileImg']); ?>" alt="User" class="w-8 h-8 rounded-full border-2 border-[var(--color-border)] shadow object-cover hover:scale-110 transition-transform">
                            <?php else: ?>
                                <div class="w-8 h-8 flex items-center justify-center rounded-full bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white text-sm font-semibold border-2 border-[var(--color-border)] shadow hover:scale-110 transition-transform">
                                    <?php echo htmlspecialchars($profileData['initials']); ?>
                                </div>
                            <?php endif; ?>
                        </button>

                        <div id="dropdownMenu" class="absolute right-0 mt-2 w-64 hidden bg-[var(--color-dropdown-bg)] border border-[var(--color-border)] rounded-lg shadow-lg z-50">
                            <div class="flex items-center px-4 py-3 mb-2 border-b border-[var(--color-border)]">
                                <?php if ($profileData['profileImg']): ?>
                                    <img id="userProfileImage" src="<?php echo htmlspecialchars(__PATH_TEMPLATE_IMG__ . $profileData['profileImg']); ?>" alt="User" class="w-10 h-10 rounded-full border-2 border-[var(--color-border)] shadow object-cover mr-3">
                                <?php else: ?>
                                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white text-sm font-semibold border border-[var(--color-border)] mr-3">
                                        <?php echo $profileData['initials']; ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-semibold text-[var(--color-heading)]"><?php echo htmlspecialchars($profileData['fullName']); ?></div>
                                    <div class="text-xs text-[var(--color-text-muted)]"><?php echo htmlspecialchars($profileData['email']); ?></div>
                                </div>
                            </div>

                            <?php if ($profileData['hasAdminAccess']): ?>
                                <a href="<?php echo __PATH_ADMINCP_HOME__; ?>" target="_blank"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-dropdown-hover)] hover:text-[var(--color-primary)] transition-all rounded-md mx-2 mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Panel administrador
                                </a>
                            <?php endif; ?>

                            <?php if ($profileData['hasEvaluatorAccess']): ?>
                                <a href="<?php echo __BASE_URL__.'/app' ?>" target="_blank"
                                    class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-text)] hover:bg-[var(--color-dropdown-hover)] hover:text-[var(--color-primary)] transition-all rounded-md mx-2 mb-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Panel evaluador
                                </a>
                            <?php endif; ?>

                            <div class="border-t my-2 border-[var(--color-border)]"></div>
                            <?php templateBuildUsercp(); ?>
                            <div class="border-t my-2 border-[var(--color-border)]"></div>

                            <div class="px-4 py-2 hover:bg-[var(--color-danger)]/10 cursor-pointer transition-colors rounded-md mx-2 mb-2">
                                <a href="<?php echo __BASE_URL__; ?>logout/" class="block text-sm text-[var(--color-danger)] font-medium">Cerrar sesión</a>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <a href="<?php echo __BASE_URL__; ?>login/" 
                       class="px-4 py-2 bg-gradient-to-br from-[var(--color-primary)] to-[var(--color-secondary)] text-white rounded-lg hover:opacity-90 transition-all text-sm font-medium shadow-md">
                        Iniciar Sesión
                    </a>
                <?php } ?>

                <button id="mobileMenuButton" class="md:hidden p-2 rounded-lg hover:bg-[var(--color-dropdown-hover)] transition-colors">
                    <svg id="menuIcon" class="w-5 h-5 text-[var(--color-navbar-text)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="closeIcon" class="w-5 h-5 text-[var(--color-navbar-text)] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobileMenu" class="md:hidden hidden bg-[var(--color-navbar-bg)] backdrop-blur-md border-t border-[var(--color-border)]">
        <div class="px-4 py-4 space-y-2">
            <?php templateBuildNavbarMobile(); ?>
        </div>
    </div>
</nav>

<script>
function marcarComoLeida(id, btn) {
    const formData = new FormData();
    formData.append('mark_read_id', id);

    fetch(location.href, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const li = btn.closest('li');
            li?.remove();
            
            const badge = document.querySelector('#notificationBtn span');
            if (badge) {
                const count = parseInt(badge.textContent) - 1;
                if (count > 0) {
                    badge.textContent = count;
                } else {
                    badge.remove();
                }
            }
        }
    });
}

function toggleTheme() {
    const root = document.documentElement;
    const themeToggle = document.getElementById('themeToggle');
    const isDark = !root.classList.contains('dark');
    
    if (isDark) {
        root.classList.add('dark');
        themeToggle.checked = true;
        localStorage.setItem('theme', 'dark');
    } else {
        root.classList.remove('dark');
        themeToggle.checked = false;
        localStorage.setItem('theme', 'light');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const theme = localStorage.getItem('theme');
    const themeToggle = document.getElementById('themeToggle');
    
    if (theme === 'dark') {
        document.documentElement.classList.add('dark');
        if (themeToggle) themeToggle.checked = true;
    } else {
        document.documentElement.classList.remove('dark');
        if (themeToggle) themeToggle.checked = false;
    }
    
    if (themeToggle) {
        themeToggle.addEventListener('change', toggleTheme);
    }

    const mobileMenuButton = document.getElementById("mobileMenuButton");
    const mobileMenu = document.getElementById("mobileMenu");
    const menuIcon = document.getElementById("menuIcon");
    const closeIcon = document.getElementById("closeIcon");
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener("click", function(e) {
            e.stopPropagation();
            
            mobileMenu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });
    }

    document.addEventListener('click', function(e) {
        if (mobileMenu && !mobileMenu.classList.contains('hidden') && 
            !mobileMenu.contains(e.target) && 
            !mobileMenuButton.contains(e.target)) {
            
            mobileMenu.classList.add('hidden');
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    });

    const userBtn = document.getElementById("userBtn");
    const dropdownMenu = document.getElementById("dropdownMenu");
    const notificationBtn = document.getElementById("notificationBtn");
    const notificationDropdown = document.getElementById("notificationDropdown");

    if (userBtn && dropdownMenu) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('hidden');
        });
    }

    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
        });
    }

    document.addEventListener('click', function() {
        if (dropdownMenu && !dropdownMenu.classList.contains('hidden')) {
            dropdownMenu.classList.add('hidden');
        }
        if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
            notificationDropdown.classList.add('hidden');
        }
    });

    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('main-nav');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
});
</script>

<style>
#main-nav {
    transition: all 0.3s ease;
}

#main-nav.scrolled {
    background: var(--color-navbar-bg) !important;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

#mobileMenu {
    max-height: calc(100vh - 4rem);
    overflow-y: auto;
}
</style>