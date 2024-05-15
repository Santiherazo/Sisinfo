<?php
function templateBuildNavbar() {
	$cfg = loadConfig('navbar');
	if(!is_array($cfg)) return;
	
	foreach($cfg as $element) {
		if(!is_array($element)) continue;
		
		# active
		if(!$element['active']) continue;
		
		# type
		$link = ($element['type'] == 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);
		
		# visibility
		if($element['visibility'] == 'guest') if(isLoggedIn()) continue;
		if($element['visibility'] == 'user') if(!isLoggedIn()) continue;
		
		# print
		if($element['newtab']) {
			echo '<li><a href="'.$link.'" target="_blank">'.$element['phrase'].'</a></li>';
		} else {
			echo '<li><a href="'.$link.'">'.$element['phrase'].'</a></li>';
		}
	}
}