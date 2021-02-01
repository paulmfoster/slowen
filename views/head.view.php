<!DOCTYPE html>
<html>
  <head>
  <meta http-equiv="content-type" content="application/xhtml+xml" charset="<?php echo $this->cfg['charset']; ?>" />
    <meta name="author" content="Paul M. Foster" />
    <meta name="generator" content="vim, php" />

<link rel="shortcut icon" href="<?php echo $this->cfg['base_url']; ?>favicon.ico">
<!-- reload CSS each time -->
<link rel="stylesheet" type="text/css" href="<?php echo $this->cfg['base_url']; ?>slowen.css?v=<?php echo date('His'); ?>">

    <title><?php echo $page_title; ?></title>
  </head>

<?php if (isset($focus_field)): ?>
	<body onLoad="document.getElementById('<?php echo $focus_field; ?>').focus();">
<?php else: ?>
  <body>
<?php endif; ?>
<a href="#top">
    <!-- For non-visual user agents: -->
      <div id="top"><a href="#main-copy" class="doNotDisplay doNotPrint">Skip to main content.</a></div>


    <!-- ##### Header ##### -->

    <div id="header">
	  	<h1 class="header-title">
		<span style="color: yellow"><?php echo $this->cfg['app_name']; ?></span>
		&nbsp;
		(<?php echo $_SESSION['entity_name'] ?? 'NONE'; ?>)
		&nbsp;
		<span><?php echo '&nbsp;' . $page_title; ?></span>
		</h1>
    </div>


    <!-- ##### Side Bar ##### -->

<div id="side-bar">
<?php echo $this->nav->show(); ?>
</div>

    <!-- ##### Main Copy ##### -->

    <div id="main-copy">

        <a name="Top"></a> 

<!-- MESSAGES ------------------------------>
<?php show_messages(); ?>
<!-- END OF MESSAGES ---------------------->

<!-- Main view file below -->
