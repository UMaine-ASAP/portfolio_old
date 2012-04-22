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
require_once 'controllers/media.php';

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
		$app->redirect($GLOBALS['web_root'].'/portfolio');
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
		$app->redirect($GLOBALS['web_root'].'/portfolio');
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
		$app->redirect($GLOBALS['web_root'].'/portfolio');
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
	if (!AuthenticationController::check_login())
	{
		return $app->render('register.html');		
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/portfolio');
	}
});

$app->post('/register', function() use ($app) {
	if (true)
	{
		// Reserved for Timothy D. Baker
	}
	else
	{
	}
});


/**
 *	View Portfolio
 */
$app->get('/portfolio', function() use ($app) {					
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
$app->get('/project/add', function() use ($app) {
	//TODO: Handle error messages from failes adds
	if (AuthenticationController::check_login())
	{
		$app->render('edit_project.html', array('project' => -1));	//TODO: Deal with this better
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	View Project
 */
$app->get('/project/:id', function($id) use ($app) {
	//TODO: Handle error messages from failed edits
	if (AuthenticationController::check_login())
	{
		if (!$proj = ProjectController::viewProject($id))
		{	// User does not have permission to view this Project
			$app->render('permission_denied.html');
		}
		else
		{
			$app->render('view_project.html');	//TODO: Add Project details
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
$app->get('/project/:id/edit', function($id) use ($app) {					
	if (AuthenticationController::check_login())
	{
		if ((!$proj = ProjectController::viewProject($id) ||
			(!$proj->havePermissionOrHigher(OWNER))))
		{	// User does not have permission to edit this Project
			return $app->render('permission_denied.html');
		}
		else
		{
			return $app->render('edit_project.html');	//TODO: Add Project details
		}
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});

$app->post('/project/:id/edit', function($id) use ($app) {
	if (AuthenticationController::check_login())
	{
		if ($id == -1)
		{	// Sent from add_project, we need to create a Project
			if (!isset($_POST['title']))
			{
				$app->flashNow('error', true);
				$app->redirect($GLOBALS['web_root'].'/project/add');	//TODO: Save partial title/desc on return to form
			}
			else
			{
				$proj = ProjectController::createProject($_POST['title'],
					(isset($_POST['description']) ? $_POST['description'] : NULL),
					1);
				$nmd_port = getNMDPortfolio();
				PortfolioController::addProjectToPortfolio($nmd_port->id(), $proj->id());
				$id = $proj->id();
			}
		}
		else
		{
			if (!ProjectController::editProject($id, 
				(isset($_POST['title']) ? $_POST['title'] : NULL),
				(isset($_POST['description']) ? $_POST['description'] : NULL),
				NULL))
			{
				$app->flashNow('error', true);
			}
		}
		$app->redirect($GLOBALS['web_root'].'/project/'.$id);
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});



/**
 *	Delete Project
 */
$app->post('/project/:id/delete', function($id) use ($app) {
	if (AuthenticationController::check_login())
	{
		if ((!$proj = ProjectController::viewProject($id) ||
			(!$proj->havePermissionOrHigher(OWNER))))
		{	// User does not have permission to edit this Project
			return $app->render('permission_denied.html');
		}
		else
		{
			ProjectController::deleteProject($id);
			$app->redirect($GLOBALS['web_root'].'/portfolio');
		}
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	Add Media
 */
$app->get('/project/:id/media/add', function($id) use ($app) {
	//TODO: Handle error messages from failed adds
	if (AuthenticationController::check_login())
	{
		$app->render('edit_media.html', array('media' => -1));	//TODO: Deal with this better
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	View Media
 */
$app->get('project/:pid/media/:id', function($pid, $id) use ($app) {
});	//TODO


/**
 *	Edit Media
 */
$app->get('project/:pid/media/:id/edit', function($pid, $id) use ($app) {
	if (AuthenticationController::check_login())
	{
		$app->render('edit_media.html', array('media' => $id));
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});

$app->post('project/:pid/media/:id/edit', function($pid, $id) use ($app) {
	if (AuthenticationController::check_login())
	{
		if ((!$proj = ProjectController::viewProject($pid)) ||
			(!$proj->havePermissionOrHigher(OWNER)))
		{
			$app->render('permission_denied.html');
		}
		else
		{
			if ($id == -1)
			{	// Sent from add_media, we need to create a New Media (hehehe)
				if (!isset($_POST['title']) || !isset($_POST['filename']) || !isset($_POST['filesize']) ||
					!isset($_POST['md5']) || !isset($_POST['extension']) || !isset($_POST['type']))
				{
					$app->flashNow('error', true);
					$app->redirect($GLOBALS['web_root'].'/project/:id/media/add');	//TODO: Save partial fields on return to form
				}
				else
				{
					$media = MediaController::createMedia($_POST['type'],
						$_POST['title'],
						(isset($_POST['description']) ? $_POST['description'] : NULL),
						$_POST['filename'],
						$_POST['filesize'],
						$_POST['md5'],
						$_POST['extension']);
					ProjectController::addMediaToProject($proj->id(), $media->id());
					$id = $media->id();
				}
			}
			else
			{
				if (!MediaController::editMedia($id,
					(isset($_POST['type']) ? $_POST['type'] : NULL),
					(isset($_POST['title']) ? $_POST['title'] : NULL),
					(isset($_POST['description']) ? $_POST['description'] : NULL),
					(isset($_POST['filename']) ? $_POST['filename'] : NULL),
					(isset($_POST['filesize']) ? $_POST['filesize'] : NULL),
					(isset($_POST['md5']) ? $_POST['md5'] : NULL),
					(isset($_POST['extension']) ? $_POST['extension'] : NULL)))
				{
					$app->flashNow('error', true);
				}
			}
			$app->redirect($GLOBALS['web_root'].'/project/'.$id);
		}
	}
	else
	{
		$app->redirect($GLOBALS['web_root'].'/login');
	}
});


/**
 *	Delete Media
 */
$app->post('/project/:pid/media/:id/delete', function($pid, $id) use ($app) {
});	//TODO


/**
 *	Review Portfolio
 */
$app->get('/portfolio/review', function() use ($app) {					
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
 *	Portfolio Submission
 */
$app->get('/portfolio/submit', function() use ($app) {					
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
