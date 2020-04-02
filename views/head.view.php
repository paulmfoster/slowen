<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-US">
  <head>
  <meta http-equiv="content-type" content="application/xhtml+xml;" charset="<?php echo $cfg['charset']; ?>" />
    <meta name="author" content="Paul M. Foster" />
    <meta name="generator" content="vim, php" />

<link rel="shortcut icon" href="<?php echo $favicon; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>">

    <title><?php echo $page_title; ?></title>
  </head>

<?php if (isset($focus_field)): ?>
	<body onLoad="document.getElementById('<?php echo $focus_field; ?>').focus();">
<?php else: ?>
  <body>
<?php endif; ?>
    <!-- For non-visual user agents: -->
      <div id="top"><a href="#main-copy" class="doNotDisplay doNotPrint">Skip to main content.</a></div>

    <!-- ##### Header ##### -->

    <div id="header">
      <h1 class="header-title">
		<a href="#top"><?php echo $app_name; ?>&nbsp;
<?php
if (isset($_SESSION['entity_name']))
	echo '(' . $_SESSION['entity_name'] . ')';
else
	echo '(NONE)';
?>
<span>
<?php echo '&nbsp;' . $page_title; ?></span></a>
      </h1>

      <div class="header-links">
      </div>
    </div>

    <!-- ##### Side Bar ##### -->

    <div id="side-bar">
		<?php echo hiernavs($nav_links); ?>
      <p class="side-bar-title">Problem or Comment?</p>
      <span class="side-bar-text">
        If you find a bug or would like to request a new feature, go 
		<a href="<?php echo $base_url . 'bugs.php'; ?>" title="Bug/FReq Form">here</a>
      </span>
    </div>

    <!-- ##### Main Copy ##### -->

    <div id="main-copy">

        <a name="Top"></a> 

<!-- MESSAGES ------------------------------>
<?php show_messages(); ?>
<!-- END OF MESSAGES ---------------------->

<!-- Main view file below -->

