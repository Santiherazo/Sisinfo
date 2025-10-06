<?php
echo '<h1 class="page-header">UserCP Menu</h1>';

try {
	
	if(isset($_GET['delete'])) {
		try {
			# cfg
			$newCfg = loadConfig('usercp');
			if(!is_array($newCfg)) throw new Exception('Usercp configs empty.');
			
			if(!isset($_GET['delete'])) throw new Exception('Invalid id.');
			if(!array_key_exists($_GET['delete'], $newCfg)) throw new Exception('Invalid id.');
			
			unset($newCfg[$_GET['delete']]);
			
			# encode
			$usercpJson = json_encode($newCfg, JSON_PRETTY_PRINT);
			
			# save changes
			$cfgFile = fopen(__PATH_CONFIGS__.'usercp.json', 'w');
			if(!$cfgFile) throw new Exception('There was a problem opening the usercp file.');
			fwrite($cfgFile, $usercpJson);
			fclose($cfgFile);
			
			message('success', 'Changes successfully saved!');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	
	if(isset($_POST['usercp_submit'])) {
		try {
			# cfg
			$newCfg = loadConfig('usercp');
			if(!is_array($newCfg)) throw new Exception('Usercp configs empty.');
			
			if(!isset($_POST['usercp_id'])) throw new Exception('Please fill all the form fields.');
			if(!isset($_POST['usercp_type'])) throw new Exception('Please fill all the form fields.');
			if(!isset($_POST['usercp_phrase'])) throw new Exception('Please fill all the form fields.');
			if(!isset($_POST['usercp_link'])) throw new Exception('Please fill all the form fields.');
			if(!in_array($_POST['usercp_type'], array('internal','external'))) throw new Exception('Link type is not valid.');
			if(!in_array($_POST['usercp_visibility'], array('user','guest','always'))) throw new Exception('Link visibility is not a valid option.');
			
			$elementId = $_POST['usercp_id'];
			
			# build new element data array
			$newElementData = array(
				'active' => (bool) ($_POST['usercp_status'] == 1 ? true : false),
				'type' => $_POST['usercp_type'],
				'phrase' => $_POST['usercp_phrase'],
				'link' => $_POST['usercp_link'],
				'icon' => (isset($_POST['usercp_icon']) ? $_POST['usercp_icon'] : 'usercp_default.png'),
				'visibility' => $_POST['usercp_visibility'],
				'newtab' => (bool) ($_POST['usercp_newtab'] == 1 ? true : false),
				'order' => (int) $_POST['usercp_order']
			);
			
			# modify usercp array
			$newCfg[$elementId] = $newElementData;
			
			# sort by order
			# http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
			usort($newCfg, function($a, $b) {
				return $a['order'] - $b['order'];
			});
			
			# encode
			$usercpJson = json_encode($newCfg, JSON_PRETTY_PRINT);
			
			# save changes
			$cfgFile = fopen(__PATH_CONFIGS__.'usercp.json', 'w');
			if(!$cfgFile) throw new Exception('There was a problem opening the usercp file.');
			fwrite($cfgFile, $usercpJson);
			fclose($cfgFile);
			
			message('success', 'Changes successfully saved!');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	
	if(isset($_POST['new_submit'])) {
		try {
			# cfg
			$newCfg = loadConfig('usercp');
			if(!is_array($newCfg)) throw new Exception('Usercp configs empty.');
			
			if(!isset($_POST['usercp_type'])) throw new Exception('Please fill all the form fields.');
			if(!isset($_POST['usercp_phrase'])) throw new Exception('Please fill all the form fields.');
			if(!isset($_POST['usercp_link'])) throw new Exception('Please fill all the form fields.');
			if(!in_array($_POST['usercp_type'], array('internal','external'))) throw new Exception('Link type is not valid.');
			if(!in_array($_POST['usercp_visibility'], array('user','guest','always'))) throw new Exception('Link visibility is not a valid option.');
			
			# build new element data array
			$newElementData = array(
				'active' => (bool) ($_POST['usercp_status'] == 1 ? true : false),
				'type' => $_POST['usercp_type'],
				'phrase' => $_POST['usercp_phrase'],
				'link' => $_POST['usercp_link'],
				'icon' => (isset($_POST['usercp_icon']) ? $_POST['usercp_icon'] : 'usercp_default.png'),
				'visibility' => $_POST['usercp_visibility'],
				'newtab' => (bool) ($_POST['usercp_newtab'] == 1 ? true : false),
				'order' => (int) $_POST['usercp_order']
			);
			
			# modify usercp array
			$newCfg[] = $newElementData;
			
			# sort by order
			# http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
			usort($newCfg, function($a, $b) {
				return $a['order'] - $b['order'];
			});
			
			# encode
			$usercpJson = json_encode($newCfg, JSON_PRETTY_PRINT);
			
			# save changes
			$cfgFile = fopen(__PATH_CONFIGS__.'usercp.json', 'w');
			if(!$cfgFile) throw new Exception('There was a problem opening the usercp file.');
			fwrite($cfgFile, $usercpJson);
			fclose($cfgFile);
			
			message('success', 'Usercp successfully updated!');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	
	$cfg = loadConfig('usercp');
	if(!is_array($cfg)) throw new Exception('Usercp configs empty.');
	
	echo '<table class="min-w-full text-sm text-left text-gray-700 border border-gray-300">';
echo '<thead class="bg-gray-100">';
echo '<tr>';
echo '<th class="px-2 py-2 border"></th>';
echo '<th class="px-2 py-2 border">Order</th>';
echo '<th class="px-2 py-2 border">Status</th>';
echo '<th class="px-2 py-2 border">Link Type</th>';
echo '<th class="px-2 py-2 border">Link</th>';
echo '<th class="px-2 py-2 border">Phrase</th>';
echo '<th class="px-2 py-2 border">Icon</th>';
echo '<th class="px-2 py-2 border">Visibility</th>';
echo '<th class="px-2 py-2 border">New Tab</th>';
echo '<th class="px-2 py-2 border"></th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach($cfg as $id => $usercpElement) {
    echo '<form action="?module=usercp" method="post">';
    echo '<input type="hidden" name="usercp_id" value="'.$id.'"/>';
    echo '<tr class="border-b">';
    
    echo '<td class="text-center align-middle px-2 py-1 border"><a href="?module=usercp&delete='.$id.'" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs"><span class="fa fa-times" aria-hidden="true"></span></a></td>';
    
    echo '<td class="px-2 py-1 border"><input type="text" name="usercp_order" class="w-full px-2 py-1 border rounded" value="'.$usercpElement['order'].'"/></td>';
    
    echo '<td class="text-center align-middle px-2 py-1 border">';
    echo '<label class="mr-2"><input type="radio" name="usercp_status" value="1" '.($usercpElement['active'] ? 'checked' : '').'> Show</label>';
    echo '<label><input type="radio" name="usercp_status" value="0" '.(!$usercpElement['active'] ? 'checked' : '').'> Hide</label>';
    echo '</td>';
    
    echo '<td class="px-2 py-1 border">';
    echo '<select name="usercp_type" class="w-full px-2 py-1 border rounded">';
    echo '<option value="internal" '.($usercpElement['type'] == 'internal' ? 'selected' : '').'>internal</option>';
    echo '<option value="external" '.($usercpElement['type'] == 'external' ? 'selected' : '').'>external</option>';
    echo '</select>';
    echo '</td>';
    
    echo '<td class="px-2 py-1 border"><input type="text" name="usercp_link" class="w-full px-2 py-1 border rounded" value="'.$usercpElement['link'].'"/></td>';
    echo '<td class="px-2 py-1 border"><input type="text" name="usercp_phrase" class="w-full px-2 py-1 border rounded" value="'.$usercpElement['phrase'].'"/></td>';
    echo '<td class="px-2 py-1 border"><input type="text" name="usercp_icon" class="w-full px-2 py-1 border rounded" value="'.$usercpElement['icon'].'"/></td>';
    
    echo '<td class="px-2 py-1 border">';
    echo '<select name="usercp_visibility" class="w-full px-2 py-1 border rounded">';
    echo '<option value="user" '.($usercpElement['visibility'] == 'user' ? 'selected' : '').'>user</option>';
    echo '<option value="guest" '.($usercpElement['visibility'] == 'guest' ? 'selected' : '').'>guest</option>';
    echo '<option value="always" '.($usercpElement['visibility'] == 'always' ? 'selected' : '').'>always</option>';
    echo '</select>';
    echo '</td>';
    
    echo '<td class="text-center align-middle px-2 py-1 border">';
    echo '<label class="mr-2"><input type="radio" name="usercp_newtab" value="1" '.($usercpElement['newtab'] ? 'checked' : '').'> Yes</label>';
    echo '<label><input type="radio" name="usercp_newtab" value="0" '.(!$usercpElement['newtab'] ? 'checked' : '').'> No</label>';
    echo '</td>';
    
    echo '<td class="text-center align-middle px-2 py-1 border">';
    echo '<button type="submit" name="usercp_submit" value="ok" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">Save</button>';
    echo '</td>';
    echo '</tr>';
    echo '</form>';
}

# Add New Element
echo '<form action="?module=usercp" method="post">';
echo '<tr><th colspan="10" class="text-center py-4 bg-gray-50 text-base font-semibold">Add New Element</th></tr>';
echo '<tr class="border-t">';
echo '<td class="border px-2 py-1"></td>';
echo '<td class="border px-2 py-1"><input type="text" name="usercp_order" class="w-full px-2 py-1 border rounded" value="10"/></td>';

echo '<td class="text-center align-middle px-2 py-1 border">';
echo '<label class="mr-2"><input type="radio" name="usercp_status" value="1" checked> Show</label>';
echo '<label><input type="radio" name="usercp_status" value="0"> Hide</label>';
echo '</td>';

echo '<td class="border px-2 py-1">';
echo '<select name="usercp_type" class="w-full px-2 py-1 border rounded">';
echo '<option value="internal" selected>internal</option>';
echo '<option value="external">external</option>';
echo '</select>';
echo '</td>';

echo '<td class="border px-2 py-1"><input type="text" name="usercp_link" class="w-full px-2 py-1 border rounded" placeholder="usercp/myaccount"/></td>';
echo '<td class="border px-2 py-1"><input type="text" name="usercp_phrase" class="w-full px-2 py-1 border rounded" placeholder="lang_phrase_x"/></td>';
echo '<td class="border px-2 py-1"><input type="text" name="usercp_icon" class="w-full px-2 py-1 border rounded" value="usercp_default.png"/></td>';

echo '<td class="border px-2 py-1">';
echo '<select name="usercp_visibility" class="w-full px-2 py-1 border rounded">';
echo '<option value="user" selected>user</option>';
echo '<option value="guest">guest</option>';
echo '<option value="always">always</option>';
echo '</select>';
echo '</td>';

echo '<td class="text-center align-middle px-2 py-1 border">';
echo '<label class="mr-2"><input type="radio" name="usercp_newtab" value="1"> Yes</label>';
echo '<label><input type="radio" name="usercp_newtab" value="0" checked> No</label>';
echo '</td>';

echo '<td class="text-center align-middle px-2 py-1 border">';
echo '<button type="submit" name="new_submit" value="ok" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Add</button>';
echo '</td>';
echo '</tr>';
echo '</form>';

echo '</tbody>';
echo '</table>';
	
	
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}