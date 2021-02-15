<!DOCTYPE html>
<html>
  	<head>
  		<meta http-equiv="content-type" content="application/xhtml+xml" charset="<?php echo $cfg['charset']; ?>" />
		<meta name="author" content="Paul M. Foster" />
		<meta name="generator" content="vim, php" />

		<link rel="shortcut icon" href="<?php echo $cfg['base_url']; ?>favicon.ico">
		<!-- reload CSS each time -->
		<link rel="stylesheet" type="text/css" href="<?php echo $cfg['base_url']; ?>slowen.css?v=<?php echo date('His'); ?>">

		<title><?php echo $page_title; ?></title>
	</head>

<?php if (isset($focus_field)): ?>
	<body onLoad="document.getElementById('<?php echo $focus_field; ?>').focus();">
<?php else: ?>
	<body>
<?php endif; ?>

<a href="#top"></a>

	<div id="header-block">
		<h1 class="header-title">
		<span style="color: #993366"><?php echo $cfg['app_name']; ?></span>
		&nbsp;
		(<?php echo $_SESSION['entity_name'] ?? 'NONE'; ?>)
		&nbsp;
		<span><?php echo '&nbsp;' . $page_title; ?></span>
		</h1>
	</div>

<div id="left-block">
<?php include $cfg['viewdir'] . 'menu.html'; ?>
</div>

<!--div id="side-bar">
echo $nav->show(); ?>
</div> -->

<div id="main-block">

<!-- MESSAGES ------------------------------>
<?php show_messages(); ?>
<!-- END OF MESSAGES ---------------------->

<!-- Main view file below -->
