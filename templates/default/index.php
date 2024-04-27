<?php
if(!defined('access') or !access) die();
include('inc/template.functions.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title></title>
		<meta name="generator" content="WebEngine"/>
		<meta name="author" content=""/>
		<meta name="description" content=""/>
		<meta name="keywords" content=""/>
		<meta property="og:type" content="website" />
		<meta property="og:title" content="" />
		<meta property="og:description" content="" />
		<meta property="og:image" content="" />
		<meta property="og:url" content="<?php echo __BASE_URL__; ?>" />
		<meta property="og:site_name" content="" />
		<link rel="shortcut icon" href=""/>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Cinzel" rel="stylesheet">
		<link href="<?php echo __PATH_TEMPLATE_CSS__; ?>style.css" rel="stylesheet" media="screen">
		<link href="<?php echo __PATH_TEMPLATE_CSS__; ?>profiles.css" rel="stylesheet" media="screen">
		<link href="<?php echo __PATH_TEMPLATE_CSS__; ?>castle-siege.css" rel="stylesheet" media="screen">
		<link href="<?php echo __PATH_TEMPLATE_CSS__; ?>override.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
		<script>
			var baseUrl = '<?php echo __BASE_URL__; ?>';
		</script>
	</head>
<header>
    <nav class="navbar">
      <a href="<?php echo __BASE_URL__; ?>" class="logo">
        <img src="images/logo.svg" alt="Sisinfo">
      </a>
      <ul class="menu-links">
        <li><a href="#">Noticias</a></li>
        <li><a href="#">Investigación</a></li>
        <li><a href="#">Eventos</a></li>
        <li><a href="#">Noticias</a></li>
        <li><a href="login">Iniciar sesión</a></li>
        <li class="join-btn"><a href="register">Unirse</a></li>
        <span id="close-menu-btn" class="material-symbols-outlined">close</span>
      </ul>
      <span id="hamburger-btn" class="material-symbols-outlined">menu</span>
    </nav>
  </header>

  <section class="hero-section">
    <div class="content">
      <h1>Find the right freelance service, right away</h1>
      <form action="#" class="search-form">
        <input type="text" placeholder="Search for any service..." required>
        <button class="material-symbols-outlined" type="sumbit">search</button>
      </form>
      <div class="popular-tags">
        Popular:
        <ul class="tags">
          <li><a href="#">Webite Design</a></li>
          <li><a href="#">Logo Design</a></li>
          <li><a href="#">WordPress</a></li>
          <li><a href="#">AI Design</a></li>
        </ul>
      </div>
    </div>
  </section>

    <footer class="footer">
        <?php include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/footer.php'); ?>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="<?php echo __PATH_TEMPLATE_JS__; ?>main.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</html>