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
require_once 'controllers/portfolio.php';
require_once 'controllers/project.php';

// Library configuration
TwigView::$twigDirectory = __DIR__ . '/libraries/Twig/lib/Twig/';

ORM::configure("mysql:host=$HOST;dbname=$DATABASE");
ORM::configure('username', $USERNAME);
ORM::configure('password', $PASSWORD);


function redirect( $destination ){
	$GLOBALS['app']->redirect($GLOBALS['web_root'] . $destination);
}



/************************************************
 * ROUTING!!									*
 ***********************************************/

/**
 *	System Home
 */
$app = new Slim(array(
	'view' => new TwigView
));
// Inform app of the web root for the next HTML request
$app->flashNow('web_root', $GLOBALS['web_root']);


/**
 *	Webroot
 */
$app->get('/', function() use ($app) {
	if (AuthenticationController::check_login())
	{
		$app->redirect($GLOBALS['web_root'].'/view_portfolio');
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	Login
 */
$app->get('/login', function() use ($app) {
	if (AuthenticationController::check_login())
	{	// User is already logged in
		$app->redirect($GLOBALS['web_root'].'/view_portfolio');
	}
	else
	{
		return $app->render('login.html');
	}
});

$app->post('/login', function() use ($app) {
	if (isset($_POST['username']) && isset($_POST['password']) &&
		AuthenticationController::attempt_login($_POST['username'], $_POST['password']))
	{	// Success!
		$app->redirect($GLOBALS['web_root'].'/view_portfolio');
	}
	else
	{	// Fail :(
		return $app->render('failed_login.html');
	}
});


/**
 *	Log Out
 */
$app->get('/logout', function() use ($app) {
	AuthenticationController::log_out();	
	return $app->render('logout.html');		
});


/**
 *	Register
 */
$app->get('/register', function() use ($app) {					
	if (AuthenticationController::check_login())
	{
		return $app->render('register.html');		
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});

$app->post('/register', function() use ($app) {
	if (AuthenticationController::check_login())
	{

	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	View Portfolio
 */
$app->get('/view_portfolio', function() use ($app) {					
	if (AuthenticationController::check_login())
	{
		$nmd_port = getNMDPortfolio();

		// Create multi-dimensional array of Project properties
		$projects = array();
		if ($nmd_port)
		{
			foreach ($nmd_port->children as $child_id=>$arr)
			{
				$proj = ProjectController::viewProject($child_id);	// assume all children are Projects
				$projects[] = array($proj->id(), $proj->title, $proj->description, $proj->type);
			}
		}
		
		return $app->render('view_portfolio.html', array('projects' => $projects));		
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	Add Project
 */
$app->get('/add_project', function() use ($app) {					
	if (AuthenticationController::check_login())
	{
		return $app->render('add_project.html');		
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});

$app->post('/add_project', function() use ($app) {
	if (AuthenticationController::check_login())
	{
		if (!isset($_POST['title']) || !isset($_POST['description']))
		{
			$app->flashNow('error', true);
			$app->redirect($GLOBALS['web_root'].'/add_project');
		}
		else
		{
			$proj = ProjectController::createProject($_POST['title'], $_POST['description'], 1);
			$nmd_port = getNMDPortfolio();
			PortfolioController::addProjectToPortfolio($nmd_port->id(), $proj->id());
			$app->redirect($GLOBALS['web_root'].'/view_project/'.$proj->id());
		}
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	Edit Project
 */
$app->get('/edit_project', function() use ($app) {					
	if (AuthenticationController::check_login())
	{
		return $app->render('edit_project.html');		
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	View Project
 */
$app->get('/view_project/:id', function($id) use ($app) {
	if (AuthenticationController::check_login())
	{

	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	Review Portfolio
 */
$app->get('/review_portfolio', function() use ($app) {					
	if (AuthenticationController::check_login())
	{
		return $app->render('review_portfolio.html');		
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	Portfolio Submitted
 */
$app->get('/portfolio_submitted', function() use ($app) {					
	if (AuthenticationController::check_login())
	{
		return $app->render('portfolio_submitted.html');		
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});



// RUN THE THING
$app->run();




/****************************************
 * HELPER FUNCTIONS						*
 ***************************************/

function getNMDPortfolio()
{
	$port = PortfolioController::getOwnedPortfolios();
	$nmd_port = null;
	foreach ($port as $p)
	{
		if ($p->title == "New Media Freshman Portfolio 2012")
		{
			$nmd_port = $p;
			break;
		}
	}
	
	return $nmd_port;
}

?>
