<?php

/**
 * This file was created, with controller, model and view smashed
 * together, because other implementations tended to result in infinite
 * recursions. It just seemed simpler to have entity.php its own single
 * file with its only dependencies being the header and footer views and
 * the CSS and favicon files.
 */

/**********************************************************************
 * Copyright Section
 **********************************************************************/

/**
 * @package apps
 * @copyright  2017, Paul M. Foster <paulf@quillandmouse.com>
 * @author Paul M. Foster <paulf@quillandmouse.com>
 * @license LICENSE file
 * @version 2.0
 */

// ================== Definitions

/// ================= Application specific variables

$cfg = array();
$cfg['programmer_email'] = "paulf@dudley.mars.lan";
$cfg['charset'] = "us-ascii";
$cfg['language'] = "en_us";

$return_to = 'index.php';
$app_subdir = 'slowen';
$app_nick = 'slowen';
$app_name = 'Slowen';
$app_prefix = 'sl';
$app_links = array(
	array('url' => $return_to, 'txt' => 'Home')
);

/// ================= End of application specific variables

$page_title = 'Select Entity';

$protocol = 'http://';
$http_host = $_SERVER['HTTP_HOST'];

$base_dir = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR;
$base_url = sprintf("%s%s/%s%s", $protocol, $http_host, $app_subdir, DIRECTORY_SEPARATOR);

$css = $base_url . $app_nick . '.css';
$favicon = $base_url . 'favicon.ico';

$func_links = array(
	array('url' => 'index.php', 'txt' => 'Home')
);


// ================== Session details

ini_set('session.gc_maxlifetime', 2592000);
ini_set('session.cookie_lifetime', 2592000);
session_set_cookie_params(2592000);
session_name($app_nick);
session_start();

// ================== Define entities

$entities = array(
	array(
		'entity_num' => 1,
		'entity_name' => 'Personal'
	),
	array(
		'entity_num' => 2,
		'entity_name' => 'Business'
	)
);

// ================== External functions needed

include 'includes/navigation.inc.php';
include 'includes/messages.inc.php';
include 'navlinks.php';

// ================== User input handling
	
if (!empty($_POST)) {
	$num = count($entities);
	for ($i = 0; $i < $num; $i++) {
		if ($_POST['entity_num'] == $entities[$i]['entity_num']) {
			$_SESSION['entity_num'] = $_POST['entity_num'];
			$_SESSION['entity_name'] = $entities[$i]['entity_name'];
			$_SESSION['messages'][] = 'S0012 Entity has been set to ' . $_SESSION['entity_name'] . '.';
			break;
		}
	}
}

// ================== header view

include $base_dir . 'views/head.view.php';

// ================== Main contents view

?>

<form method="post" action="entity.php">

<table>
<?php $num = count($entities); ?>
<?php for ($i = 0; $i < $num; $i++): ?>
<tr>
	<td>
	<h2>
	<input type="radio" id="entity_num" name="entity_num" value="<?php echo $entities[$i]['entity_num']; ?>" <?php if ($i == 0) echo 'checked="checked"'; ?>/>
	</h2>
	</td>
	<td>
	<h2>
		<?php echo $entities[$i]['entity_name']; ?>
	</h2>
	</td>
</tr>
<?php endfor; ?>
</table>
<p>
<input type="submit" id="selected" name="selected" value="Select"/>
</p>

</form>

<?php

// ================== footer view

include $base_dir . 'views/footer.view.php';


