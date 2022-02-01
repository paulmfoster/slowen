<!DOCTYPE html>
<html>
  	<head>
  		<meta http-equiv="content-type" content="application/xhtml+xml" charset="<?php echo $cfg['charset']; ?>" />
		<meta name="author" content="Paul M. Foster" />
		<meta name="generator" content="vim, php" />

		<link rel="shortcut icon" href="favicon.ico">
		<!-- reload CSS each time -->
		<link rel="stylesheet" type="text/css" href="style.css?v=<?php echo date('His'); ?>">

		<title><?php echo $page_title; ?></title>
	</head>

<?php if (isset($focus_field)): ?>
	<body onLoad="document.getElementById('<?php echo $focus_field; ?>').focus();">
<?php else: ?>
	<body>
<?php endif; ?>

<!-- HEADER -->
<a href="#top"></a>
<div class="container">
<div id="header">
	<h1 class="header-title">
	<?php if (!isset($_SESSION) || !array_key_exists('entity_name', $_SESSION)): ?>
	<span class="app-name"><?php echo $cfg['app_name']; ?></span>
	<?php else: ?>
	<span class="app-name"><?php echo $_SESSION['entity_name']; ?></span>
	<?php endif; ?>
	&nbsp;
	<span><?php echo '&nbsp;' . $page_title; ?></span>
	</h1>
</div>
<!-- END OF HEADER -->

<div id="left-nav">
<?php echo $nav->show(); ?>
</div>

<div id="content">

<!-- MESSAGES -->
<?php show_messages(); ?>
<!-- END OF MESSAGES -->

<!-- Main view file below -->
