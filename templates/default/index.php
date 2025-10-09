<?php
if (!defined('access') || !access) die();
include('inc/template.functions.php');

$page = $_REQUEST['page'] ?? '';
$subpage = $_REQUEST['subpage'] ?? '';
?>
<!DOCTYPE html>
<html lang="es" class="transition duration-300">
<head>
  <meta charset="utf-8" />
  <title><?php $handler->websiteTitle(); ?></title>
  <meta name="generator" content="WebCMS <?php echo __WEBENGINE_VERSION__; ?>" />
  <meta name="author" content="<?php config('author'); ?>" />
  <meta name="description" content="<?php config('website_meta_description'); ?>" />
  <meta name="keywords" content="<?php config('website_meta_keywords'); ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?php $handler->websiteTitle(); ?>" />
  <meta property="og:description" content="<?php config('website_meta_description'); ?>" />
  <meta property="og:image" content="<?php echo __PATH_TEMPLATE_IMG__ . (config('website_meta_og_image')); ?>" />
  <meta property="og:url" content="<?php echo __BASE_URL__; ?>" />
  <meta property="og:site_name" content="<?php $handler->websiteTitle(); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="<?php echo __PATH_TEMPLATE__ . 'favicon.ico'; ?>">
  <link href="<?php echo __PATH_TEMPLATE_CSS__; ?>style.css" rel="stylesheet" media="screen" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://kit.fontawesome.com/a2f1c3ad60.js" crossorigin="anonymous"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            'color-bg': 'var(--color-bg)',
            'color-text': 'var(--color-text)',
            'color-primary': 'var(--color-primary)',
            'color-secondary': 'var(--color-secondary)',
            'color-accent': 'var(--color-accent)',
            'color-border': 'var(--color-border)',
            'color-surface': 'var(--color-surface)',
            'color-surface-alt': 'var(--color-surface-alt)',
            'color-dropdown-hover': 'var(--color-dropdown-hover)',
            'color-dropdown-bg': 'var(--color-dropdown-bg)',
            'color-link': 'var(--color-link)',
            'color-link-hover': 'var(--color-link-hover)',
            'color-notification-bg': 'var(--color-accent)'
          },
          borderRadius: {
            custom: '0.5rem'
          },
          fontFamily: {
            custom: ['Inter', 'sans-serif']
          }
        }
      }
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="bg-[var(--color-bg)] text-[var(--color-text)] font-custom">
  <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/navbar.php'); ?>
  <div id="container" class="min-h-screen bg-[var(--color-bg)] text-[var(--color-text)] flex flex-col">
    <main id="content" class="flex-grow w-full mx-auto transition-all duration-300">
      <?php $handler->loadModule($page ?: 'home', $subpage); ?>
    </main>
    <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/footer.php'); ?>
    <script src="<?php echo __PATH_TEMPLATE_JS__; ?>main.js"></script>
  </div>
</body>
</html>