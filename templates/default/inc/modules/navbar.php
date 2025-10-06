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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imgElement = document.getElementById('userProfileImage');
    const baseImgUrl = '<?php echo $profileData['profileImg']; ?>';
    
    if (imgElement && baseImgUrl) {
        imgElement.src = baseImgUrl;
        
        setInterval(() => {
            const timestamp = new Date().getTime();
            imgElement.src = `${baseImgUrl}?t=${timestamp}`;
        }, 30000);
    }
});
</script>

<header id="navbar" class="fixed w-full z-50 top-0 px-4 md:px-16 py-3 bg-[var(--color-navbar-bg)] text-[var(--color-navbar-text)] shadow transition duration-300 ease-in-out">
    <div class="max-w-[1440px] mx-auto flex items-center justify-between">
        <div class="flex md:hidden">
            <button id="mobileMenuBtn" class="focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <div class="absolute left-1/2 transform -translate-x-1/2 md:static md:transform-none md:left-0 flex items-center space-x-2">
            <a href="<?php echo __BASE_URL__; ?>" class="flex items-center space-x-2">
                <img src="<?php echo __PATH_TEMPLATE_IMG__ . (config('website_logo') ?? 'logo.png'); ?>" alt="Logo" class="w-6 h-6">
                <span class="font-semibold text-lg"><?php config('website_name'); ?></span>
            </a>
        </div>

        <?php templateBuildNavbar(); ?>

        <div class="flex items-center space-x-4 relative">
            <div class="fixed bottom-6 right-6 z-50">
                <div class="flex items-center">
                    <label for="themeToggle" class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="themeToggle" class="sr-only peer" onchange="toggleTheme()"/>
                        <div class="w-14 h-7 bg-[var(--color-border-muted)] rounded-full transition-all duration-300 dark:bg-[var(--color-border)]">
                            <div class="absolute flex justify-between items-center w-14 px-2 h-7 text-xs text-white select-none pointer-events-none">
                                <span>🌞</span>
                                <span>🌙</span>
                            </div>
                            <div class="absolute top-0.5 left-0.5 w-6 h-6 bg-[var(--color-accent)] rounded-full shadow transform transition-all duration-300 peer-checked:translate-x-7"></div>
                        </div>
                    </label>
                    <span class="ml-3 text-sm text-[var(--color-text-muted)]">Modo oscuro</span>
                </div>
            </div>
            
            <?php if (isLoggedIn()) { ?>
                <div class="relative group">
                    <button id="notificationBtn" class="relative focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <?php if ($profileData['unreadCount'] > 0) { ?>
                            <span class="absolute -top-1 -right-1 bg-[var(--color-danger)] text-white rounded-full text-xs px-1.5 py-0.5"><?php echo $profileData['unreadCount']; ?></span>
                        <?php } ?>
                    </button>
                    <div class="absolute right-0 mt-2 w-60 sm:w-80 hidden group-hover:block bg-[var(--color-dropdown-bg)] text-[var(--color-text)] border border-[var(--color-border)] rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">
                        <div class="flex justify-between items-center p-4 border-b border-[var(--color-border)] font-semibold">
                            <span>Notificaciones</span>
                            <a href="<?php echo __BASE_URL__; ?>notificaciones" class="text-xs text-[var(--color-link)] hover:underline">Ver todas</a>
                        </div>
                        <?php if (!empty($profileData['notifications'])) { ?>
                            <ul class="divide-y divide-[var(--color-border)]"> 
                                <?php foreach ($profileData['notifications'] as $notif) { ?>
                                    <li class="p-3 hover:bg-[var(--color-dropdown-hover)] text-sm flex justify-between items-start">
                                        <div>
                                            <?php echo htmlspecialchars($notif['message']); ?>
                                            <div class="text-xs text-[var(--color-text-muted)] mt-1"><?php echo date('d M H:i', strtotime($notif['created_at'])); ?></div>
                                        </div>
                                        <button onclick="marcarComoLeida(<?php echo $notif['id']; ?>, this)"
                                                class="text-xs text-[var(--color-link)] hover:underline ml-2">
                                            Marcar
                                        </button>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <div class="p-4 text-sm text-[var(--color-text-muted)]">No tienes notificaciones nuevas.</div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="relative group">
                <button id="userBtn" class="focus:outline-none">
                    <?php if (isLoggedIn()): ?>
                        <?php if (!empty($profileData['profileImg'])): ?>
                            <img id="userProfileImage" src="<?php echo htmlspecialchars(__PATH_TEMPLATE_IMG__ . $profileData['profileImg']); ?>" alt="User" class="w-10 h-10 rounded-full border-2 border-[var(--color-border)] shadow object-cover">
                        <?php else: ?>
                            <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-600 text-white text-sm font-semibold border-2 border-[var(--color-border)] shadow">
                                <?php echo htmlspecialchars($profileData['initials']); ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?php echo __PATH_TEMPLATE_IMG__ . 'avatars/guest.png'; ?>" alt="Guest" class="w-10 h-10 rounded-full border-2 border-[var(--color-border)] shadow object-cover">
                    <?php endif; ?>
                </button>

                <div id="dropdownMenu" class="absolute right-0 mt-2 w-64 hidden group-hover:block bg-[var(--color-dropdown-bg)] text-[var(--color-text)] border border-[var(--color-border)] rounded-lg shadow-lg z-50">
                    <?php if (isLoggedIn()) { ?>
                        <div class="flex items-center px-4 py-3 mb-2 border-b border-[var(--color-border)]">
                            <?php if ($profileData['profileImg']): ?>
                                <img id="userProfileImage" src="<?php echo htmlspecialchars(__PATH_TEMPLATE_IMG__ . $profileData['profileImg']); ?>" alt="User" class="w-10 h-10 rounded-full border-2 border-[var(--color-border)] shadow object-cover">
                            <?php else: ?>
                                <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-600 text-white text-sm font-semibold border border-[var(--color-border)] mr-3">
                                    <?php echo $profileData['initials']; ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="font-semibold text-[var(--color-text)]"><?php echo htmlspecialchars($profileData['fullName']); ?></div>
                                <div class="text-xs text-[var(--color-text-muted)]"><?php echo htmlspecialchars($profileData['email']); ?></div>
                            </div>
                        </div>

                        <?php if ($profileData['hasAdminAccess']): ?>
                            <a href="<?php echo __PATH_ADMINCP_HOME__; ?>" target="_blank"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-link)] hover:text-white hover:bg-[var(--color-accent)] transition rounded-md">
                                <i class="fas fa-cogs"></i> Panel administrador
                            </a>
                        <?php endif; ?>

                        <?php if ($profileData['hasEvaluatorAccess']): ?>
                            <a href="<?php echo __BASE_URL__.'/app' ?>" target="_blank"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-[var(--color-link)] hover:text-white hover:bg-[var(--color-accent)] transition rounded-md">
                                <i class="fas fa-clipboard-check"></i> Panel evaluador
                            </a>
                        <?php endif; ?>

                        <div class="border-t my-2 border-[var(--color-border)]"></div>

                        <?php templateBuildUsercp(); ?>

                        <div class="border-t my-2 border-[var(--color-border)]"></div>

                        <div class="px-4 py-2 hover:bg-[var(--color-dropdown-hover)] cursor-pointer">
                            <a href="<?php echo __BASE_URL__; ?>logout/" class="block text-sm text-[var(--color-danger)]">Cerrar sesión</a>
                        </div>
                    <?php } else { ?>
                        <div class="px-4 py-2 hover:bg-[var(--color-dropdown-hover)] cursor-pointer">
                            <a href="<?php echo __BASE_URL__; ?>login/" class="block text-sm">Iniciar sesión</a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php templateBuildNavbarMobile(); ?>
</header>

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

document.addEventListener("DOMContentLoaded", function () {
    const toggleHover = (trigger, dropdown) => {
        let timeout;
        trigger.addEventListener("mouseenter", () => {
            clearTimeout(timeout);
            dropdown.classList.remove("hidden");
        });
        trigger.addEventListener("mouseleave", () => {
            timeout = setTimeout(() => dropdown.classList.add("hidden"), 300);
        });
        dropdown.addEventListener("mouseenter", () => clearTimeout(timeout));
        dropdown.addEventListener("mouseleave", () => {
            timeout = setTimeout(() => dropdown.classList.add("hidden"), 300);
        });
    };

    const userBtn = document.getElementById("userBtn");
    const dropdownMenu = document.getElementById("dropdownMenu");
    const notificationBtn = document.getElementById("notificationBtn");
    const notificationDropdown = notificationBtn?.nextElementSibling;

    if (userBtn && dropdownMenu) toggleHover(userBtn, dropdownMenu);
    if (notificationBtn && notificationDropdown) toggleHover(notificationBtn, notificationDropdown);

    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const mobileMenu = document.getElementById("mobileMenu");
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener("click", () => {
            mobileMenu.classList.toggle("hidden");
        });
    }
});

function toggleTheme() {
    const root = document.documentElement;
    const isDark = root.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    document.getElementById('themeToggle').checked = isDark;
}

document.addEventListener('DOMContentLoaded', () => {
    const theme = localStorage.getItem('theme');
    const isDark = theme === 'dark';
    if (isDark) document.documentElement.classList.add('dark');
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) themeToggle.checked = isDark;
});
</script>