<?php
echo '<h1 class="page-header">Navigation Menu</h1>';

try {
    if(isset($_GET['delete'])) {
        try {
            $newCfg = loadConfig('navbar');
            if(!is_array($newCfg)) throw new Exception('Navbar configs empty.');
            if(!isset($_GET['delete'])) throw new Exception('Invalid id.');
            if(!array_key_exists($_GET['delete'], $newCfg)) throw new Exception('Invalid id.');
            unset($newCfg[$_GET['delete']]);
            $navbarJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__.'navbar.json', 'w');
            if(!$cfgFile) throw new Exception('There was a problem opening the navbar file.');
            fwrite($cfgFile, $navbarJson);
            fclose($cfgFile);
            message('success', 'Changes successfully saved!');
        } catch(Exception $ex) {
            message('error', $ex->getMessage());
        }
    }

    if(isset($_POST['navbar_submit'])) {
        try {
            $newCfg = loadConfig('navbar');
            if(!is_array($newCfg)) throw new Exception('Navbar configs empty.');
            if(!isset($_POST['navbar_id'])) throw new Exception('Please fill all the form fields.');
            if(!isset($_POST['navbar_type'])) throw new Exception('Please fill all the form fields.');
            if(!isset($_POST['navbar_phrase'])) throw new Exception('Please fill all the form fields.');
            if(!in_array($_POST['navbar_type'], array('internal','external'))) throw new Exception('Link type is not valid.');
            if(!in_array($_POST['navbar_visibility'], array('user','guest','always'))) throw new Exception('Link visibility is not a valid option.');
            $elementId = $_POST['navbar_id'];
            $newElementData = array(
                'active' => (bool) ($_POST['navbar_status'] == 1 ? true : false),
                'type' => $_POST['navbar_type'],
                'phrase' => $_POST['navbar_phrase'],
                'link' => (isset($_POST['navbar_link']) ? $_POST['navbar_link'] : ''),
                'visibility' => $_POST['navbar_visibility'],
                'newtab' => (bool) ($_POST['navbar_newtab'] == 1 ? true : false),
                'order' => (int) $_POST['navbar_order']
            );
            $newCfg[$elementId] = $newElementData;
            usort($newCfg, function($a, $b) {
                return $a['order'] - $b['order'];
            });
            $navbarJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__.'navbar.json', 'w');
            if(!$cfgFile) throw new Exception('There was a problem opening the navbar file.');
            fwrite($cfgFile, $navbarJson);
            fclose($cfgFile);
            message('success', 'Changes successfully saved!');
        } catch(Exception $ex) {
            message('error', $ex->getMessage());
        }
    }

    if(isset($_POST['new_submit'])) {
        try {
            $newCfg = loadConfig('navbar');
            if(!is_array($newCfg)) throw new Exception('Navbar configs empty.');
            if(!isset($_POST['navbar_type'])) throw new Exception('Please fill all the form fields.');
            if(!isset($_POST['navbar_phrase'])) throw new Exception('Please fill all the form fields.');
            if(!in_array($_POST['navbar_type'], array('internal','external'))) throw new Exception('Link type is not valid.');
            if(!in_array($_POST['navbar_visibility'], array('user','guest','always'))) throw new Exception('Link visibility is not a valid option.');
            $newElementData = array(
                'active' => (bool) ($_POST['navbar_status'] == 1 ? true : false),
                'type' => $_POST['navbar_type'],
                'phrase' => $_POST['navbar_phrase'],
                'link' => (isset($_POST['navbar_link']) ? $_POST['navbar_link'] : ''),
                'visibility' => $_POST['navbar_visibility'],
                'newtab' => (bool) ($_POST['navbar_newtab'] == 1 ? true : false),
                'order' => (int) $_POST['navbar_order']
            );
            $newCfg[] = $newElementData;
            usort($newCfg, function($a, $b) {
                return $a['order'] - $b['order'];
            });
            $navbarJson = json_encode($newCfg, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_CONFIGS__.'navbar.json', 'w');
            if(!$cfgFile) throw new Exception('There was a problem opening the navbar file.');
            fwrite($cfgFile, $navbarJson);
            fclose($cfgFile);
            message('success', 'Navbar successfully updated!');
        } catch(Exception $ex) {
            message('error', $ex->getMessage());
        }
    }

    $cfg = loadConfig('navbar');
    if(!is_array($cfg)) throw new Exception('Navbar configs empty.');

} catch(Exception $ex) {
    message('error', $ex->getMessage());
    $cfg = array();
}
?>
<style>
    .glassmorphism {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-hover:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .sidebar-transition {
        transition: transform 0.3s ease-in-out;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 1rem;
        padding: 2rem;
        max-width: 90vw;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .tab-active {
        background: rgba(59, 130, 246, 0.1);
        border-bottom: 2px solid #3b82f6;
        color: #3b82f6;
    }

    .progress-bar {
        transition: width 0.3s ease-in-out;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #3b82f6;
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }
    
    .menu-item-row {
        transition: all 0.3s ease;
    }
    
    .menu-item-row:hover {
        background: rgba(255, 255, 255, 0.9);
    }
    
    .radio-group {
        display: flex;
        gap: 8px;
    }
    
    .radio-option {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .form-input {
        border: 1px solid rgba(203, 213, 225, 0.5);
        border-radius: 0.75rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.6);
        transition: all 0.2s;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    
    .form-select {
        border: 1px solid rgba(203, 213, 225, 0.5);
        border-radius: 0.75rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.6);
        transition: all 0.2s;
    }
    
    .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
    }
    
    .view-button.active {
        background-color: #3b82f6;
        color: white;
    }
</style>

<div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6 fade-in">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 mb-2">Navigation Menu Items</h2>
            <p class="text-slate-600">Manage the navigation items displayed in the website header</p>
        </div>
        <button onclick="exportMenuConfig()" class="flex items-center gap-2 px-4 py-2 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export Config
        </button>
    </div>
    
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/20 text-left text-sm text-slate-600">
                    <th class="pb-3 pl-2 pr-4">Delete</th>
                    <th class="pb-3 px-4">Order</th>
                    <th class="pb-3 px-4">Status</th>
                    <th class="pb-3 px-4">Link Type</th>
                    <th class="pb-3 px-4">Link</th>
                    <th class="pb-3 px-4">Language Phrase</th>
                    <th class="pb-3 px-4">Visibility</th>
                    <th class="pb-3 px-4">New Tab</th>
                    <th class="pb-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/20">
                <?php foreach($cfg as $id => $item): ?>
                <tr class="menu-item-row">
                    <form action="?module=navbar" method="post">
                        <td class="py-4 pl-2 pr-4">
                            <button type="button" onclick="showConfirmModal('Delete Menu Item','Are you sure you want to delete this menu item? This action cannot be undone.','delete', '<?php echo $id;?>')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                        <td class="py-4 px-4">
                            <input type="hidden" name="navbar_id" value="<?php echo htmlspecialchars($id); ?>">
                            <input type="number" name="navbar_order" value="<?php echo htmlspecialchars($item['order']); ?>" min="1" class="form-input w-16">
                        </td>
                        <td class="py-4 px-4">
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="navbar_status" value="1" <?php echo ($item['active'] ? 'checked' : ''); ?>>
                                    <span class="text-sm">Show</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="navbar_status" value="0" <?php echo (!$item['active'] ? 'checked' : ''); ?>>
                                    <span class="text-sm">Hide</span>
                                </label>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <select name="navbar_type" class="form-select">
                                <option value="internal" <?php echo ($item['type']=='internal' ? 'selected' : ''); ?>>Internal</option>
                                <option value="external" <?php echo ($item['type']=='external' ? 'selected' : ''); ?>>External</option>
                            </select>
                        </td>
                        <td class="py-4 px-4">
                            <input type="text" name="navbar_link" value="<?php echo htmlspecialchars($item['link']); ?>" class="form-input w-full">
                        </td>
                        <td class="py-4 px-4">
                            <input type="text" name="navbar_phrase" value="<?php echo htmlspecialchars($item['phrase']); ?>" class="form-input w-full">
                        </td>
                        <td class="py-4 px-4">
                            <select name="navbar_visibility" class="form-select">
                                <option value="user" <?php echo ($item['visibility']=='user' ? 'selected' : ''); ?>>User</option>
                                <option value="guest" <?php echo ($item['visibility']=='guest' ? 'selected' : ''); ?>>Guest</option>
                                <option value="always" <?php echo ($item['visibility']=='always' ? 'selected' : ''); ?>>Always</option>
                            </select>
                        </td>
                        <td class="py-4 px-4">
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="navbar_newtab" value="1" <?php echo ($item['newtab'] ? 'checked' : ''); ?>>
                                    <span class="text-sm">Yes</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="navbar_newtab" value="0" <?php echo (!$item['newtab'] ? 'checked' : ''); ?>>
                                    <span class="text-sm">No</span>
                                </label>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <button type="submit" name="navbar_submit" value="ok" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg text-sm">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Save
                            </button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>

                <tr class="menu-item-row bg-blue-50/30">
                    <form action="?module=navbar" method="post">
                        <td class="py-4 pl-2 pr-4">
                            <button type="button" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </td>
                        <td class="py-4 px-4">
                            <input type="number" name="navbar_order" value="10" min="1" class="form-input w-16">
                        </td>
                        <td class="py-4 px-4">
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="navbar_status" value="1" checked>
                                    <span class="text-sm">Show</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="navbar_status" value="0">
                                    <span class="text-sm">Hide</span>
                                </label>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <select name="navbar_type" class="form-select">
                                <option value="internal">Internal</option>
                                <option value="external">External</option>
                            </select>
                        </td>
                        <td class="py-4 px-4">
                            <input type="text" name="navbar_link" placeholder="rankings/resets" class="form-input w-full">
                        </td>
                        <td class="py-4 px-4">
                            <input type="text" name="navbar_phrase" placeholder="lang_phrase_x" class="form-input w-full">
                        </td>
                        <td class="py-4 px-4">
                            <select name="navbar_visibility" class="form-select">
                                <option value="user">User</option>
                                <option value="guest">Guest</option>
                                <option value="always">Always</option>
                            </select>
                        </td>
                        <td class="py-4 px-4">
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="navbar_newtab" value="1">
                                    <span class="text-sm">Yes</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="navbar_newtab" value="0" checked>
                                    <span class="text-sm">No</span>
                                </label>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <button type="submit" name="new_submit" value="ok" class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all shadow-lg text-sm">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Add
                            </button>
                        </td>
                    </form>
                </tr>
            </tbody>
        </table>
    </div>
    
    <div class="flex justify-between items-center mt-6 pt-6 border-t border-white/20">
        <div class="text-sm text-slate-600">
            Showing <?php echo count($cfg); ?> menu items
        </div>
        <div class="flex gap-3">
            <button onclick="saveAllChanges()" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg">
                <i data-lucide="save-all" class="w-4 h-4"></i>
                Save All Changes
            </button>
            <button onclick="resetToDefault()" class="flex items-center gap-2 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all">
                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                Reset to Default
            </button>
        </div>
    </div>
</div>
        
<div class="glassmorphism rounded-2xl p-6 shadow-xl mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 mb-2">Navigation Menu Preview</h2>
            <p class="text-slate-600">Preview how the menu will appear to users</p>
        </div>
        <div class="flex gap-2">
            <button id="user-view-btn" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm font-medium view-button active" onclick="togglePreviewView('user')">User View</button>
            <button id="guest-view-btn" class="px-3 py-1 bg-white/60 border border-white/20 rounded-lg text-sm font-medium view-button" onclick="togglePreviewView('guest')">Guest View</button>
        </div>
    </div>
    
    <div class="bg-white/80 rounded-xl p-6 shadow-inner">
        <div class="flex flex-wrap gap-2" id="preview-container">
            <?php 
            function shouldDisplayItem($item, $viewType) {
                if (!$item['active']) return false;
                
                if ($viewType === 'user') {
                    return $item['visibility'] === 'user' || $item['visibility'] === 'always';
                } else {
                    return $item['visibility'] === 'guest' || $item['visibility'] === 'always';
                }
            }
            
            $userItems = array_filter($cfg, function($item) { return shouldDisplayItem($item, 'user'); });
            $guestItems = array_filter($cfg, function($item) { return shouldDisplayItem($item, 'guest'); });
            
            usort($userItems, function($a, $b) { return $a['order'] - $b['order']; });
            usort($guestItems, function($a, $b) { return $a['order'] - $b['order']; });
            ?>
            
            <div id="user-preview" class="preview-view">
                <?php foreach($userItems as $item): ?>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-50 border border-blue-100 text-blue-700 font-medium">
                    <?php echo htmlspecialchars($item['phrase']); ?>
                    <?php if($item['newtab']): ?>
                    <i data-lucide="external-link" class="w-4 h-4 text-blue-500"></i>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div id="guest-preview" class="preview-view hidden">
                <?php foreach($guestItems as $item): ?>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-50 border border-blue-100 text-blue-700 font-medium">
                    <?php echo htmlspecialchars($item['phrase']); ?>
                    <?php if($item['newtab']): ?>
                    <i data-lucide="external-link" class="w-4 h-4 text-blue-500"></i>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div id="confirm-modal" class="modal">
    <div class="modal-content w-full max-w-md">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-900" id="confirm-title">Confirm Action</h2>
            <button onclick="closeModal('confirm-modal')" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="mb-6">
            <p id="confirm-message">Are you sure you want to perform this action?</p>
        </div>
        
        <div class="flex gap-4 pt-4">
            <button onclick="closeModal('confirm-modal')" class="flex-1 px-6 py-3 bg-white/60 border border-white/20 rounded-xl hover:bg-white/80 transition-all font-medium">
                Cancel
            </button>
            <button onclick="confirmAction()" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl hover:from-red-600 hover:to-pink-700 transition-all font-medium shadow-lg">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
    if(typeof lucide !== 'undefined' && lucide.createIcons) {
        lucide.createIcons();
    }

    let sidebarOpen = false;
    let userMenuOpen = false;
    let currentAction = null;
    let currentItemId = null;

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

    function showConfirmModal(title, message, action, itemId = null) {
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        document.getElementById('confirm-modal').classList.add('show');
        document.body.style.overflow = 'hidden';
        
        currentAction = action;
        currentItemId = itemId;
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
        document.body.style.overflow = 'auto';
        
        currentAction = null;
        currentItemId = null;
    }

    function confirmAction() {
        if (currentAction === 'delete' && currentItemId) {
            window.location.href = '?module=navbar&delete=' + encodeURIComponent(currentItemId);
            return;
        } else if (currentAction === 'reset') {
            alert('Reset to default not implemented on the server.');
        }
        
        closeModal('confirm-modal');
    }

    function deleteMenuItem(itemId) {
        showConfirmModal(
            'Delete Menu Item', 
            'Are you sure you want to delete this menu item? This action cannot be undone.', 
            'delete', 
            itemId
        );
    }

    function saveMenuItem(itemId) {
        const form = document.querySelector('input[name="navbar_id"][value="'+itemId+'"]')?.closest('form');
        if(form) form.submit();
    }

    function addMenuItem() {
        const addForm = document.querySelector('form button[name="new_submit"]')?.closest('form');
        if(addForm) addForm.submit();
    }

    function saveAllChanges() {
        alert('Use each row\'s Save button to save changes. Implement batch save server-side if needed.');
    }

    function resetToDefault() {
        showConfirmModal(
            'Reset to Default', 
            'Are you sure you want to reset the menu configuration to defaults? All custom changes will be lost.', 
            'reset'
        );
    }

    function exportMenuConfig() {
        alert('Exporting menu configuration: download not implemented. Implement server-side endpoint to expose JSON if needed.');
    }

    function togglePreviewView(viewType) {
        const userBtn = document.getElementById('user-view-btn');
        const guestBtn = document.getElementById('guest-view-btn');
        const userPreview = document.getElementById('user-preview');
        const guestPreview = document.getElementById('guest-preview');
        
        if (viewType === 'user') {
            userBtn.classList.add('active', 'bg-blue-500', 'text-white');
            userBtn.classList.remove('bg-white/60');
            guestBtn.classList.remove('active', 'bg-blue-500', 'text-white');
            guestBtn.classList.add('bg-white/60');
            userPreview.classList.remove('hidden');
            guestPreview.classList.add('hidden');
        } else {
            guestBtn.classList.add('active', 'bg-blue-500', 'text-white');
            guestBtn.classList.remove('bg-white/60');
            userBtn.classList.remove('active', 'bg-blue-500', 'text-white');
            userBtn.classList.add('bg-white/60');
            guestPreview.classList.remove('hidden');
            userPreview.classList.add('hidden');
        }
    }

    document.addEventListener('click', function(event) {
        const userMenu = document.getElementById('user-menu');
        const userMenuButton = event.target.closest('[onclick="toggleUserMenu()"]');
        
        if (!userMenuButton && userMenu && !userMenu.contains(event.target) && userMenuOpen) {
            toggleUserMenu();
        }
    });

    document.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            if (event.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if(sidebar) sidebar.classList.remove('-translate-x-full');
            if(overlay) overlay.classList.add('hidden');
            sidebarOpen = false;
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        console.log('Navigation Menu Configuration initialized');
    });
</script>