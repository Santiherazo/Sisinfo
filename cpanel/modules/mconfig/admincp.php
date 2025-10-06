<?php
echo '<h1 class="page-header">AdminCP Menu</h1>';

try {
    $cfgFilePath = __PATH_CONFIGS__.'admincp.json';

    // Load current config
    $config = file_exists($cfgFilePath) ? json_decode(file_get_contents($cfgFilePath), true) : [];

    if(!is_array($config)) $config = [];

    // Re-index config by numeric ID
    $config = array_values($config);
    foreach($config as $k => $v) {
        if(!isset($v['id'])) $config[$k]['id'] = $k + 1;
    }

    // --- Move position logic ---
    if(isset($_GET['move']) && isset($_GET['id'])) {
        $id = (int) $_GET['id'];
        $direction = $_GET['move'];

        $index = array_search($id, array_column($config, 'id'));
        if($index === false) throw new Exception('Invalid ID.');

        if($direction === 'up' && $index > 0) {
            [$config[$index - 1], $config[$index]] = [$config[$index], $config[$index - 1]];
            [$config[$index - 1]['id'], $config[$index]['id']] = [$config[$index]['id'], $config[$index - 1]['id']];
        }
        if($direction === 'down' && $index < count($config) - 1) {
            [$config[$index + 1], $config[$index]] = [$config[$index], $config[$index + 1]];
            [$config[$index + 1]['id'], $config[$index]['id']] = [$config[$index]['id'], $config[$index + 1]['id']];
        }

        file_put_contents($cfgFilePath, json_encode($config, JSON_PRETTY_PRINT));
        header("Location: ?module=admincp");
        exit;
    }

    // --- Delete logic ---
    if(isset($_GET['delete'])) {
        $idToDelete = (int) $_GET['delete'];
        $config = array_values(array_filter($config, fn($item) => $item['id'] !== $idToDelete));
        file_put_contents($cfgFilePath, json_encode($config, JSON_PRETTY_PRINT));
        message('success', 'Item deleted successfully.');
    }

    // --- Save existing menu ---
    if(isset($_POST['submit_admincp'])) {
        $id = (int) $_POST['admincp_id'];
        $title = $_POST['admincp_title'] ?? '';
        $icon = $_POST['admincp_icon'] ?? '';
        $keys = $_POST['admincp_item_keys'] ?? [];
        $values = $_POST['admincp_item_values'] ?? [];

        if(empty($title)) throw new Exception('Title is required.');
        if(count($keys) != count($values)) throw new Exception('Mismatched keys and values.');

        $items = [];
        foreach($keys as $k => $key) {
            $val = $values[$k];
            if(!empty($key) && !empty($val)) {
                $items[$key] = $val;
            }
        }

        foreach($config as &$menu) {
            if($menu['id'] == $id) {
                $menu['title'] = $title;
                $menu['icon'] = $icon;
                $menu['items'] = $items;
                break;
            }
        }

        file_put_contents($cfgFilePath, json_encode($config, JSON_PRETTY_PRINT));
        message('success', 'Changes saved successfully.');
    }

    // --- Add new menu ---
    if(isset($_POST['new_admincp'])) {
        $title = $_POST['admincp_title'] ?? '';
        $icon = $_POST['admincp_icon'] ?? '';
        $keys = $_POST['admincp_item_keys'] ?? [];
        $values = $_POST['admincp_item_values'] ?? [];

        if(empty($title)) throw new Exception('Title is required.');

        $items = [];
        foreach($keys as $k => $key) {
            $val = $values[$k];
            if(!empty($key) && !empty($val)) {
                $items[$key] = $val;
            }
        }

        $maxId = empty($config) ? 0 : max(array_column($config, 'id'));
        $newId = $maxId + 1;

        $config[] = [
            'id' => $newId,
            'title' => $title,
            'icon' => $icon,
            'items' => $items
        ];

        file_put_contents($cfgFilePath, json_encode($config, JSON_PRETTY_PRINT));
        message('success', 'New menu added.');
    }

    // --- Display table ---
    echo '<table class="min-w-full text-sm text-left text-gray-700 border border-gray-300">';
    echo '<thead class="bg-gray-100">';
    echo '<tr><th class="px-2 py-2 border">ID</th><th class="px-2 py-2 border">Title</th><th class="px-2 py-2 border">Icon</th><th class="px-2 py-2 border">Items</th><th class="px-2 py-2 border">Action</th></tr>';
    echo '</thead><tbody>';

    foreach($config as $menu) {
        $id = $menu['id'];
        echo '<form method="post" action="?module=admincp">';
        echo '<input type="hidden" name="admincp_id" value="'.$id.'">';
        echo '<tr class="border-b">';
        echo '<td class="px-2 py-1 border">'.$id.'</td>';
        echo '<td class="px-2 py-1 border"><input name="admincp_title" value="'.htmlspecialchars($menu['title']).'" class="w-full border px-2 py-1 rounded"/></td>';
        echo '<td class="px-2 py-1 border"><input name="admincp_icon" value="'.htmlspecialchars($menu['icon']).'" class="w-full border px-2 py-1 rounded"/></td>';
        echo '<td class="px-2 py-1 border" id="edit-fields-'.$id.'">';
        foreach($menu['items'] as $key => $label) {
            echo '<div class="flex gap-1 mb-1">';
            echo '<input type="text" name="admincp_item_keys[]" value="'.htmlspecialchars($key).'" class="border px-2 py-1 rounded w-1/2" placeholder="Key">';
            echo '<input type="text" name="admincp_item_values[]" value="'.htmlspecialchars($label).'" class="border px-2 py-1 rounded w-1/2" placeholder="Value">';
            echo '</div>';
        }
        echo '</td>';
        echo '<td class="px-2 py-1 border text-center">';
        echo '<button name="submit_admincp" value="1" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Save</button><br>';
        echo '<a href="?module=admincp&delete='.$id.'" class="text-red-500 text-sm">Delete</a><br>';
        echo '<a href="?module=admincp&move=up&id='.$id.'" class="text-blue-500 text-xs">↑ Move Up</a> | ';
        echo '<a href="?module=admincp&move=down&id='.$id.'" class="text-blue-500 text-xs">↓ Move Down</a><br>';
        echo '<button type="button" onclick="addEditField('.$id.')" class="mt-1 text-blue-500 text-sm">+ Add Item</button>';
        echo '</td>';
        echo '</tr>';
        echo '</form>';
    }

    // --- Add New Menu Row ---
    echo '<form method="post" action="?module=admincp">';
    echo '<tr><th colspan="5" class="bg-gray-50 py-3 text-center">Add New Menu</th></tr>';
    echo '<tr class="border-t">';
    echo '<td class="px-2 py-1 border">New</td>';
    echo '<td class="px-2 py-1 border"><input name="admincp_title" class="w-full border px-2 py-1 rounded"/></td>';
    echo '<td class="px-2 py-1 border"><input name="admincp_icon" class="w-full border px-2 py-1 rounded"/></td>';
    echo '<td class="px-2 py-1 border" id="new-item-fields"></td>';
    echo '<td class="px-2 py-1 border text-center">';
    echo '<button name="new_admincp" value="1" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Add</button>';
    echo '<button type="button" onclick="addNewItemField()" class="mt-2 block text-blue-500 text-sm">+ Add Item</button>';
    echo '</td>';
    echo '</tr>';
    echo '</form>';

    echo '</tbody></table>';

    // --- JS for dynamic fields ---
    echo '<script>
    function createItemFieldHTML() {
        return `
            <div class="flex gap-1 mb-1">
                <input type="text" name="admincp_item_keys[]" class="border px-2 py-1 rounded w-1/2" placeholder="Key">
                <input type="text" name="admincp_item_values[]" class="border px-2 py-1 rounded w-1/2" placeholder="Value">
            </div>
        `;
    }

    function addNewItemField() {
        const container = document.getElementById("new-item-fields");
        container.insertAdjacentHTML("beforeend", createItemFieldHTML());
    }

    function addEditField(id) {
        const container = document.getElementById(`edit-fields-${id}`);
        container.insertAdjacentHTML("beforeend", createItemFieldHTML());
    }
    </script>';

} catch(Exception $ex) {
    message('error', $ex->getMessage());
}