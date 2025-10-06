<?php

function templateBuildNavbar() {
    $cfg = loadConfig('navbar');
    if (!is_array($cfg)) return;

    echo '<nav class="hidden md:flex absolute left-1/2 transform -translate-x-1/2 items-center space-x-6 text-sm font-medium">';
    
    foreach ($cfg as $element) {
        if (!is_array($element) || !$element['active']) continue;

        $visibleToGuest = $element['visibility'] === 'guest' && isLoggedIn();
        $visibleToUser  = $element['visibility'] === 'user' && !isLoggedIn();
        if ($visibleToGuest || $visibleToUser) continue;

        $link  = ($element['type'] === 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);
        $title = htmlspecialchars($element['phrase'] ?: 'Unk_phrase');
        $target = $element['newtab'] ? ' target="_blank"' : '';
        $isActive = (strpos($_SERVER['REQUEST_URI'], $element['link']) !== false) ? 'active' : '';

        echo '<a href="' . $link . '"' . $target . ' class="relative py-1.5 text-[var(--color-navbar-text)] hover:text-[var(--color-secondary)] transition-colors duration-200 group">';
        echo $title;
        echo '<span class="absolute bottom-0 left-0 w-0 h-px bg-[var(--color-secondary)] transition-all duration-300 group-hover:w-full ' . ($isActive ? '!w-full' : '') . '"></span>';
        echo '</a>';
    }

    echo '</nav>';
}

function templateBuildNavbarMobile() {
  $cfg = loadConfig('navbar');
  if (!is_array($cfg)) return;

  echo '<nav id="mobileMenu" class="md:hidden hidden mt-4 px-4 space-y-2 text-sm font-medium text-[var(--color-text)]">';
  
  foreach ($cfg as $element) {
    if (!is_array($element) || !$element['active']) continue;

    $visibleToGuest = $element['visibility'] === 'guest' && isLoggedIn();
    $visibleToUser  = $element['visibility'] === 'user' && !isLoggedIn();
    if ($visibleToGuest || $visibleToUser) continue;

    $link  = ($element['type'] === 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);
    $title = htmlspecialchars($element['phrase'] ?: 'Unk_phrase');
    $target = $element['newtab'] ? ' target="_blank"' : '';

    echo '<a href="' . $link . '"' . $target . ' class="block w-full px-4 py-2 rounded-md hover:bg-[var(--color-primary)] text-[var(--color-text)] hover:text-white transition duration-200">';
    echo $title;
    echo '</a>';
  }

  echo '</nav>';
}

function templateBuildUsercp() {
  $cfg = loadConfig('usercp');
  if (!is_array($cfg)) return;

  echo '<div class="space-y-1">';

  foreach ($cfg as $element) {
    if (!is_array($element) || !$element['active']) continue;

    $visibleToGuest = $element['visibility'] === 'guest' && isLoggedIn();
    $visibleToUser  = $element['visibility'] === 'user' && !isLoggedIn();
    if ($visibleToGuest || $visibleToUser) continue;

    $link  = ($element['type'] === 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);
    $title = htmlspecialchars($element['phrase'] ?: 'Unk_phrase');
    $target = $element['newtab'] ? ' target="_blank"' : '';

    echo '<a href="' . $link . '"' . $target . ' class="block px-4 py-2 rounded-md text-sm text-[var(--color-text)] hover:bg-[var(--color-dropdown-hover)] hover:text-[var(--color-secondary)] transition duration-200">';
    echo $title;
    echo '</a>';
  }

  echo '</div>';
}