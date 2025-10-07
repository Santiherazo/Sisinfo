<?php
if (!defined('PROFILE_CACHE_DIR')) {
    define('PROFILE_CACHE_DIR', __PATH_CACHE__ . '/profiles/');
}

if (!file_exists(PROFILE_CACHE_DIR)) {
    mkdir(PROFILE_CACHE_DIR, 0755, true);
}

$ProfileCache = new Cache();
$ProfileCache->configure('file', PROFILE_CACHE_DIR)
             ->setCachePath(PROFILE_CACHE_DIR, 'profiles');

$db = Connection::Database('sisinfo');
$pdo = $db->getConnection();
$logger = new ErrorLogger();
$profileManager = new ProfileManager($pdo, $db, $logger);

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
        $cachedProfile = $ProfileCache->getUserProfile($uid);
        
        if ($cachedProfile) {
            $profileData = $cachedProfile;
        } else {
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
                    'notifications' => [],
                    'unreadCount' => 0
                ];

                $ProfileCache->storeUserProfile($uid, $profileData, $cacheDuration);
            }
        }

        $profileData['hasAdminAccess'] = accessManager()->canAccess($uid, ['administrador', 'coordinador'], [['module' => 'Cpanel', 'action' => 'access']]);
        $profileData['hasEvaluatorAccess'] = accessManager()->canAccess($uid, ['evaluador'], [['module' => 'Epanel', 'action' => 'access']]);
        
    } catch (Throwable $e) {
        error_log('[Profile Error] ' . $e->getMessage());
    }
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imgElement = document.getElementById('userProfileImage');
    const baseImgUrl = '<?php echo $profileData['profileImg']; ?>';
    
    if (imgElement && baseImgUrl) {
        imgElement.src = baseImgUrl;
    }

    lucide.createIcons();
});
</script>

<div class="max-w-[1400px] mx-auto py-6 pt-24">
    <div class="bg-[var(--color-surface)] rounded-custom shadow-lg overflow-hidden relative animate-fadeIn border border-[var(--color-border)]">
        <div class="relative h-40 w-full flex items-center justify-between px-8 bg-gradient-to-r from-[var(--color-primary)] via-purple-600 to-blue-800">
            <div class="absolute -bottom-16 left-8 transform hover:scale-105 transition-transform duration-300">
                <div class="w-32 h-32 relative">
                    <?php if (!empty($profileData['profileImg'])): ?>
                        <img id="userProfileImage" src="" alt="Avatar" 
                             class="w-32 h-32 rounded-custom border-4 border-white shadow-xl object-cover transition-all duration-300">
                    <?php else: ?>
                        <div class="w-32 h-32 flex items-center justify-center rounded-custom bg-gradient-to-br from-[var(--color-primary)] to-purple-600 text-white text-2xl font-bold border-4 border-white shadow-xl">
                            <?php echo htmlspecialchars($profileData['initials']); ?>
                        </div>
                    <?php endif; ?>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-[var(--color-success)] rounded-full border-4 border-white flex items-center justify-center">
                        <i data-lucide="check" class="w-3 h-3 text-white"></i>
                    </div>
                </div>
            </div>

            <div class="absolute top-6 right-8 flex gap-3">
                <?php if ($profileData['hasAdminAccess']): ?>
                    <a href="<?php echo __PATH_ADMINCP_HOME__; ?>" target="_blank" 
                       class="flex items-center px-5 py-2.5 bg-white/20 backdrop-blur-sm text-white rounded-custom hover:bg-white/30 transition-all duration-300 hover:shadow-lg border border-white/30">
                        <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                        Panel Admin
                    </a>
                <?php endif; ?>

                <?php if ($profileData['hasEvaluatorAccess']): ?>
                    <a href="<?php echo __BASE_URL__.'/app'; ?>" target="_blank"
                    class="flex items-center px-5 py-2.5 bg-[var(--color-warning)] text-white rounded-custom hover:bg-yellow-600 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 transform">
                        <i data-lucide="clipboard-check" class="w-4 h-4 mr-2"></i>
                        Evaluar Proyectos
                    </a>
                <?php endif; ?>
            </div>

            <div class="absolute top-4 left-1/4 w-6 h-6 bg-white/20 rounded-full blur-sm animate-float"></div>
            <div class="absolute bottom-8 right-1/3 w-8 h-8 bg-cyan-300/30 rounded-full blur-sm animate-float" style="animation-delay: 2s;"></div>
        </div>

        <div class="pt-20 px-8 pb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-6">
                <div class="flex-1">
                    <h2 class="text-3xl font-bold text-[var(--color-heading)] mb-2"><?php echo htmlspecialchars($profileData['fullName']); ?></h2>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center text-[var(--color-text-muted)]">
                            <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                            <span class="text-lg"><?php echo htmlspecialchars($profileData['email']); ?></span>
                        </div>
                        <div class="flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <i data-lucide="check-circle" class="w-3 h-3 mr-1 fill-green-500 text-green-500"></i>
                            Verificado
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-custom flex items-center justify-center hover:bg-blue-100 transition-colors duration-300 cursor-pointer border border-[var(--color-border)]">
                        <img class="w-6 h-6" src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google" />
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-custom flex items-center justify-center hover:bg-red-100 transition-colors duration-300 cursor-pointer border border-[var(--color-border)]">
                        <img class="w-6 h-6" src="https://img.icons8.com/color/48/000000/gmail-new.png" alt="Gmail" />
                    </div>
                    <div class="w-12 h-12 bg-green-50 rounded-custom flex items-center justify-center hover:bg-green-100 transition-colors duration-300 cursor-pointer border border-[var(--color-border)]">
                        <img class="w-6 h-6" src="https://img.icons8.com/color/48/000000/google-slides.png" alt="Slides" />
                    </div>
                    <div class="w-12 h-12 bg-yellow-50 rounded-custom flex items-center justify-center hover:bg-yellow-100 transition-colors duration-300 cursor-pointer border border-[var(--color-border)]">
                        <img class="w-6 h-6" src="https://img.icons8.com/color/48/000000/google-photos.png" alt="Photos" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>