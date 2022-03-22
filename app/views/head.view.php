<!DOCTYPE html>
<html>
  	<head>
  		<meta http-equiv="content-type" content="application/xhtml+xml" charset="<?php echo $this->cfg['charset']; ?>" />
		<meta name="author" content="Paul M. Foster" />
		<meta name="generator" content="vim, php" />

        <link rel="shortcut icon" href="<?php echo VIEWDIR; ?>favicon.ico">
		<!-- reload CSS each time -->
        <link rel="stylesheet" type="text/css" href="<?php echo VIEWDIR; ?>style.css?v=<?php echo date('His'); ?>">

		<title><?php echo $this->page_title; ?></title>
	</head>

<?php if (isset($this->focus_field)): ?>
	<body onLoad="document.getElementById('<?php echo $this->focus_field; ?>').focus();">
<?php else: ?>
	<body>
<?php endif; ?>

<!-- HEADER -->
<a href="#top"></a>
<div class="container">
<div id="header">
	<h1 class="header-title">
	<span class="app-name"><?php echo $this->cfg['app_name']; ?></span>
	&nbsp;
	<span><?php echo '&nbsp;' . $this->page_title; ?></span>
	</h1>
</div>
<!-- END OF HEADER -->

<div id="left-nav">
<?php echo $this->nav->show(); ?>
</div>

<div id="content">

<!-- MESSAGES -->
<?php show_messages(); ?>
<!-- END OF MESSAGES -->

<!-- Main view file below -->
