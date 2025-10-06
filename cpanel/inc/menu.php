<nav class="space-y-4" id="sidebarMenu">
        <a href="<?= admincp_base(); ?>" class="flex items-center text-sm text-gray-700 hover:text-blue-600 px-3 py-2 rounded-lg transition-all">
          <span class="material-icons mr-2">dashboard</span> Dashboard
        </a>
        <?php foreach($admincpSidebar as $section): ?>
        <div class="space-y-1">
          <button onclick="toggleSubmenu(this)" class="flex items-center justify-between w-full text-sm text-blue-700 bg-blue-100 px-3 py-2 rounded-lg font-medium shadow-sm hover:bg-blue-200 transition">
            <span class="flex items-center">
              <span class="material-icons mr-2"><?= $section['icon'] ?></span>
              <?= $section['title'] ?>
            </span>
            <span class="change material-icons transform transition-transform duration-200">expand_more</span>
          </button>
          <div class="ml-6 mt-1 space-y-1 hidden submenu">
            <?php foreach($section['items'] as $mod => $label): ?>
              <a href="<?= admincp_base($mod) ?>" class="block text-sm text-gray-700 hover:text-blue-600 px-2 py-1 rounded-lg <?= ($_GET['module'] ?? '') === $mod ? 'font-semibold bg-blue-50' : '' ?>">
                <?= $label ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </nav>
      <script>
        function toggleSubmenu(button) {
          const submenu = button.nextElementSibling;
          const icon = button.querySelector('.change');
          submenu.classList.toggle('hidden');
          icon.classList.toggle('rotate-180');
        }
        function setActiveMenu(el) {
          document.querySelectorAll('.submenu a').forEach(a => a.classList.remove('font-semibold'));
          el.classList.add('font-semibold');
        }
      </script>