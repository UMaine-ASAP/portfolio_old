<?php
session_start();
if( isset( $_SESSION['uploadForm']) ) {
	$GLOBALS['specialUpload'] = $_SESSION;
}
session_destroy();

set_include_path("./libraries");
error_reporting(E_ALL);

require_once 'portfolio.php';

/**
 *	System Home
 */
$app = new Slim(array(
	'view' => new TwigView,
	'debug' => true
));

/**
 * Sets breadcrumbs for current page
 */
function setBreadcrumb( $breadcrumbs ) {
	$GLOBALS['app']->flashNow('breadcrumbs', $breadcrumbs);
}

/**
 * Middleware and helpers. Middleware allows you to hook Slim functions and modify/add to their behavior; helpers are just that.
 */

require_once 'helper.php';
require_once 'middleware.php';
require_once 'routing.php';


// RUN THE THING
$app->run();