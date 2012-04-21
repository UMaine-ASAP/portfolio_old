<?php
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

// Library configuration
TwigView::$twigDirectory = __DIR__ . '/libraries/Twig/lib/Twig/';

ORM::configure("mysql:host=$HOST;dbname=$DATABASE");
ORM::configure('username', $USERNAME);
ORM::configure('password', $PASSWORD);


function redirect( $destination ){
	$GLOBALS['app']->redirect($GLOBALS['web_root'] . $destination);
}

// Basic routes to each of the templates. 
// Will need to be populated with all of the modules.

// System Home
$app = new Slim(array(
	'view' => new TwigView
));

// Login
$app->get('/', function() use ($app) {					
	return $app->render('login.html');		
});

// Register
$app->get('/register', function() use ($app) {					
	return $app->render('register.html');		
});

// View Portfolio
$app->get('/view_portfolio', function() use ($app) {					
	return $app->render('view_portfolio.html');		
});
/**
 *	Posts to /view_portfolio come from the Login page when submitted
 */
$app->post('/view_portfolio', function() use ($app) {
	if (isset($_POST['name']) && isset($_POST['password']) &&
		AuthenticationController::attempt_login($_POST['username'], $_POST['password']))
	{	// Success!
		echo "Login success!";
	}
	else
	{	// Fail :(
		echo "Login failed!";
	}
});

// Add Project
$app->get('/add_project', function() use ($app) {					
	return $app->render('add_project.html');		
});

// Edit Project
$app->get('/edit_project', function() use ($app) {					
	return $app->render('edit_project.html');		
});

// Review Portfolio
$app->get('/review_portfolio', function() use ($app) {					
	return $app->render('review_portfolio.html');		
});

// Portfolio Submitted
$app->get('/portfolio_submitted', function() use ($app) {					
	return $app->render('portfolio_submitted.html');		
});

// Log Out
$app->get('/logout', function() use ($app) {					
	return $app->render('logout.html');		
});

$app->run();
?>
