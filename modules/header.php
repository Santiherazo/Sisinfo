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
});
</script>

<style>
.input-field {
    background-color: var(--color-input-bg);
    color: var(--color-input-text);
    border: 1px solid var(--color-input-border);
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    transition: border-color 0.3s;
    width: 100%;
}

.input-field:focus {
    outline: none;
    border-color: var(--color-primary);
}

.btn-save {
    background-color: var(--color-primary);
    color: var(--color-surface);
    padding: 0.5rem 1.25rem;
    font-size: 0.875rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: background-color 0.3s;
}

.btn-save:hover {
    background-color: var(--color-secondary);
}
</style>

<div class="max-w-[1440px] mx-auto px-4 ml:px-10 py-8">
    <div class="bg-[var(--color-bg)] rounded-xl shadow-md overflow-hidden relative">
        <div class="relative h-36 w-full flex items-center justify-between px-6" style="background: linear-gradient(to right, var(--color-accent), var(--color-secondary), var(--color-primary));">
            <div class="absolute -bottom-12 left-6">
                <div class="w-24 h-24 relative">
                    <?php if (!empty($profileData['profileImg'])): ?>
                        <img id="userProfileImage" src="" alt="Avatar" class="w-24 h-24 rounded-full border-4 shadow-md object-cover" style="border-color: var(--color-bg);">
                    <?php else: ?>
                        <div class="w-24 h-24 flex items-center justify-center rounded-full bg-gray-600 text-white text-sm font-semibold border-2 border-[var(--color-border)] shadow">
                            <?php echo htmlspecialchars($profileData['initials']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="absolute top-14 right-6 flex gap-3">
                <?php if ($profileData['hasAdminAccess']): ?>
                    <a href="<?php echo __PATH_ADMINCP_HOME__; ?>" target="_top" class="text-sm font-semibold px-4 py-2 rounded-lg shadow-md hover:opacity-90 transition-all" style="background-color: var(--color-danger); color: var(--color-surface);">
                        Admin
                    </a>
                <?php endif; ?>

                <?php if ($profileData['hasEvaluatorAccess']): ?>
                    <button onclick="window.open('<?php echo __BASE_URL__.'/app' ?>', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes')" 
                          class="text-sm font-semibold px-4 py-2 rounded-lg shadow-md hover:opacity-90 transition-all" 
                          style="background-color: var(--color-warning); color: var(--color-surface);">
                        Evalua aquí
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="pt-20 px-6 pb-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="relative">
                    <h2 class="text-2xl font-semibold text-[var(--color-text)]"><?php echo htmlspecialchars($profileData['fullName']); ?></h2>
                    <p class="text-sm text-[var(--color-secondary)]"><?php echo htmlspecialchars($profileData['email']); ?></p>
                </div>
                <div class="flex gap-3 mt-2 md:mt-0">
                    <img class="w-6 h-6" src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google" />
                    <img class="w-6 h-6" src="https://img.icons8.com/color/48/000000/gmail-new.png" alt="Gmail" />
                    <img class="w-6 h-6" src="https://img.icons8.com/color/48/000000/google-slides.png" alt="Slides" />
                    <img class="w-6 h-6" src="https://img.icons8.com/color/48/000000/google-photos.png" alt="Photos" />
                </div>
            </div>
            <div class="mt-3">
                <div class="bg-[var(--color-success)] text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md inline-block">
                    Activo
                </div>
            </div>
        </div>
    </div>
</div>