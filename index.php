<?php
session_start();
if( isset( $_SESSION['uploadForm']) ) {
	$GLOBALS['specialUpload'] = $_SESSION;
}

session_destroy();
set_include_path(get_include_path() . PATH_SEPARATOR . "./libraries");

error_reporting(E_ALL);

// Our Settings file matters most!
require_once 'settings.php';

// External Libraries
require_once 'Slim/Slim/Slim.php';
require_once 'Idiorm/idiorm.php';
require_once 'Paris/paris.php';
require_once 'Views/TwigView.php';

// Controllers
require_once 'controllers/authentication.php';
require_once 'controllers/assignment.php';
require_once 'controllers/portfolio.php';
require_once 'controllers/project.php';
require_once 'controllers/media.php';
require_once 'controllers/user.php';

// Library configuration
TwigView::$twigDirectory = __DIR__ . '/libraries/Twig/lib/Twig/';

ORM::configure("mysql:host=$HOST;dbname=$DATABASE");
ORM::configure('username', $USERNAME);
ORM::configure('password', $PASSWORD);

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



