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

require_once 'controllers/evaluation/form.php';
require_once 'controllers/evaluation/component.php';
require_once 'controllers/evaluation/evaluation.php';
require_once 'controllers/evaluation/evaluationAssignment.php';

// Library configuration
TwigView::$twigDirectory = __DIR__ . '/libraries/Twig/lib/Twig/';

ORM::configure("mysql:host=$HOST;dbname=$DATABASE");
ORM::configure('username', $USERNAME);
ORM::configure('password', $PASSWORD);


/**
 *	System Home
 */
$app = new Slim(array(
	'view' => new TwigView
));


/****************************************
 * HELPER FUNCTIONS						*
 ***************************************/

/**
 * Retrieve currently logged-in User's New Media 2012 porfolio.
 *
 * Returns null if not found, the Portfolio object otherwise.
 */
function getNMDPortfolio()
{
	$ports = PortfolioController::getOwnedPortfolios();
	$nmd_port = null;
	foreach ($ports as $p)
	{
		if ($p->title == "New Media Freshman Portfolio 2012")
		{
			$nmd_port = $p;
			break;
		}
	}
	
	return $nmd_port;
}

/**
 * Retrieve the AssignmentInstance for the New Media 2012 protfolio submissions.
 */
function getNMDAssignmentInstance()
{
	$instance = AssignmentController::viewAssignmentInstance(1);
	return $instance;
}

/**
 * Check whether or not the currently logged-in User's New Media 2012 portfolio has been submitted.
 *
 * Returns true if submitted, false otherwise.
 */
function portfolioIsSubmitted()
{
	// Prevent Undergrads from submitting/adding/editing/deleting after deadline in one fell swoop
	return true;

	$instance = getNMDAssignmentInstance();
	$port = getNMDPortfolio();
	foreach ($instance->children as $child_id=>$arr)
	{
		if ($child_id == $port->id())
		{
			return true;
		}
	}
	return false;
}

/**
 * Helper to redirect the User to a location with the webroot appended
 */
function redirect($destination)
{
	return $GLOBALS['app']->redirect($GLOBALS['web_root'] . $destination);
}

/**
 * Helper to handle when a User does not have permission to access a page
 */
function permission_denied()
{
	return $GLOBALS['app']->render('permission_denied.html');
}

/**
 * Sets breadcrumbs for current page
 */
function setBreadcrumb( $breadcrumbs ) {
	return $GLOBALS['app']->flashNow('breadcrumbs', $breadcrumbs);
}



/****************************************
 * MIDDLEWARE FUNCTIONS					*
 ***************************************/


/**
 * Returns true and sets 'logged_in' flash variable if a User is currently logged in,
 * returns false and redirects to /login otherwise.
 */
$authcheck_login = function () use ($app)
{
	if (!AuthenticationController::check_login())
	{
		$app->flashNow('logged_in', false);
		redirect('/login');
		return false;
	}
	else
	{
		$app->flashNow('logged_in', true);
		return true;
	}
};

/** 
 * Middleware to check student authentication and redirect to permission denied if not student 
 */
$authcheck_student = function () use ($app, $authcheck_login)
{	
	if ($authcheck_login() && AuthenticationController::currentUserIsStudent())
	{
		$app->flashNow('portfolioIsSubmitted', portfolioIsSubmitted() );
		return true;
	}
	else
	{	// Denied
		permission_denied();
		return $GLOBALS['app']->stop();
	}
};

/**
 * Middleware to check submission status of the student's Portfolio and redirect appropriately
 */
$submission_check = function () use ($app)
{
	if (portfolioIsSubmitted())
	{
		permission_denied();
		return $app->stop();
	}
};

/** 
 * Middleware to check faculty authentication and redirect to permission denied if not faculty.
 */
$authcheck_faculty = function () use ($app, $authcheck_login)
{
	if ($authcheck_login() && AuthenticationController::currentUserIsFaculty())
	{
		return true;
	}
	else
	{	// Denied
		permission_denied();
		return $GLOBALS['app']->stop();
	}
};

/**
 * Middleware to redirect user based on role to role-specific homepage
 */
$redirect_loggedInUser = function () use ($app, $authcheck_student)
{
	if (AuthenticationController::check_login())
	{	// User is already logged in
		if (AuthenticationController::currentUserIsStudent())
		{
			redirect('/portfolio');
			return false;
		}
		else if (AuthenticationController::currentUserIsFaculty())
		{
			redirect('/portfolios');
			return false;
		}
		else
		{
			permission_denied();
			return $app->stop();
		}
	}
};


/************************************************
 * ROUTING!!									*
 ***********************************************/


// Inform app of the web root for the next HTML request
$app->flashNow('web_root', $GLOBALS['web_root']);


/**
 *	Webroot
 */
$app->get('/', $authcheck_login, $redirect_loggedInUser, function() use ($app) {
});


/**
 *	Login
 */
$app->get('/login', $redirect_loggedInUser, function() use ($app) {
	return $app->render('login.html');
});

$app->post('/login', function() use ($app, $redirect_loggedInUser) {
	if (isset($_POST['username']) && isset($_POST['password']) &&
		AuthenticationController::attempt_login($_POST['username'], $_POST['password']))
	{	// Success!
		return $redirect_loggedInUser();
	}
	else
	{	// Fail :(
		$app->flash('error', 'Username or password was incorrect.');
		return redirect('/login');
	}
});


/**
 *	Log Out
 */
$app->get('/logout', $authcheck_login, function() use ($app) {
	AuthenticationController::log_out();
	$app->flash('header', 'You have been successfully logged out.');
	return redirect('/login');
});


/**
 *	Register
 */
$app->get('/register', $redirect_loggedInUser, function() use ($app) {
	// Prevent Undergrads from registering past deadline
	//return permission_denied();

	return $app->render('register.html');
});

$app->post('/register', $redirect_loggedInUser, function() use ($app) {
	// Prevent Undergrads from being sneaky past the deadline
	//return permission_denied();

	if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email']) || !isset($_POST['firstname']) || !isset($_POST['lastname']))
	{	// Reject, form invalid
		$app->flash('error', true);
		return redirect('/register');	//TODO: Save partial title/desc on return to form
	}
	else
	{
		$first = $_POST['firstname'];
		$last =  $_POST['lastname'];
		// Create new User
		if (!$user = UserController::createUser($_POST['username'],
			$_POST['password'],
			$first,
			NULL,
			$last,
			$_POST['email'],
			1,
			NULL, NULL, NULL, NULL, NULL, NULL, 2))
		{
			$app->flash('error', "Username is already in use");
			return redirect('/register');
		}
		else
		{
			// Login as new User
			AuthenticationController::attempt_login($_POST['username'], $_POST['password']);
			// Create User's NMD portfolio
			$port = PortfolioController::createPortfolio("New Media Freshman Portfolio 2012", "New Media Freshman Portfolio 2012", 1);
			// Add permission for User to submit to NMD 2012 AssignmentInstance
			$instance = getNMDAssignmentInstance();
			$instance->addPermissionForUser($user->id(), SUBMIT);
			
			return redirect('/portfolio');	// Users may only register as Undergraduates
		}
	}
});


/**
 *	View Portfolio
 */
$app->get('/portfolio', $authcheck_student, function() use ($app) {
	$nmd_port = getNMDPortfolio();

	// Create multi-dimensional array of Project properties
	$projects = array();
	if ($nmd_port)
	{
		foreach ($nmd_port->children as $child_id=>$arr)
		{
			$proj = ProjectController::viewProject($child_id);	// assume all children are Projects
			// Trim title if it is too long
			$t = substr($proj->title, 0, 50);
			if (strlen($t) < strlen($proj->title)) { $t = $t . "..."; }
			// Trim description if it is too long
			$desc = substr($proj->description, 0, 410);
			if (strlen($desc) < strlen($proj->description)) { $desc = $desc . "..."; }
			$projects[] = array("project_id" => $proj->id(), "title" => $t, "description" => $desc, "thumbnail" => $proj->thumbnail, "type" => $proj->type);
		}
	}
		
	return $app->render('view_portfolio.html', array('projects' => $projects));
});


/**
 *	Add Project
 */
$app->get('/project/add', $authcheck_student, $submission_check, function() use ($app) {
	//TODO: Handle error messages from failed adds
	return $app->render('edit_project.html', 
		array('project_id' => -1,
			'title' => "",
			'description' => ""));

});


/**
 *	View Project
 */
$app->get('/project/:id', $authcheck_student, function($id) use ($app) {

	//TODO: Handle error messages from failed edits
	if (!$proj = ProjectController::viewProject($id))
	{	// User does not have permission to view this Project
		return permission_denied();
	}
	else
	{
		setBreadcrumb( array( 
				array(	'name'=>"New Media Portfolio", 
						'url'=>'/portfolio')
				));

		$media = array();
		foreach ($proj->media as $media_id)
		{
			$m = MediaController::viewMedia($media_id);
			$media[] = array('media_id' => $m->id(),
				'mimetype' => $m->mimetype,
				'title' => $m->title,
				'description' => $m->description,
				'created' => $m->created,
				'edited' => $m->edited,
				'filename' => $m->filename,
				'filesize' => $m->filesize,
				'md5' => $m->md5,
				'extension' => $m->extension);
		}

		return $app->render('view_project.html',
			array('project_id' => $proj->id(),
				'title' => $proj->title,
				'description' => $proj->description,
				'thumbnail' => $proj->thumbnail,
				'media_items' => $media));
	}
});


/**
 *	Edit Project
 */
$app->get('/project/:id/edit', $authcheck_student, $submission_check, function($id) use ($app) {					
	if ((!$proj = ProjectController::viewProject($id)) ||
		(!$proj->havePermissionOrHigher(OWNER)))
	{	// User does not have permission to edit this Project
		return permission_denied();
	}
	else
	{

		setBreadcrumb( array( 
				array(	'name'=>"New Media Portfolio",
						'url'=>'/portfolio'),
				array(	'name'=>$proj->title,
						'url'=>'/project/'.$id),
				));

		$media = array();
		foreach ($proj->media as $media_id)
		{
			$m = MediaController::viewMedia($media_id);
			$media[] = array('media_id' => $m->id(),
				'mimetype' => $m->mimetype,
				'title' => $m->title,
				'description' => $m->description,
				'created' => $m->created,
				'edited' => $m->edited,
				'filename' => $m->filename,
				'filesize' => $m->filesize,
				'md5' => $m->md5,
				'extension' => $m->extension);
		}

		return $app->render('edit_project.html',
			array('project_id' => $id,
				'title' => $proj->title,
				'description' => $proj->description,
				'media_items' => $media));

	}
});

$app->post('/project/:id/edit', $authcheck_student, $submission_check, function($id) use ($app) {
	$thumb_path = NULL;
	if ($id == -1)
	{	// Sent from add_project, we need to create a Project
		if (!isset($_POST['title']))
		{	// Reject, form invalid
			$app->flash('error', true);
			return redirect('/project/add');	//TODO: Save partial title/desc on return to form
		}
		else
		{
			$proj = ProjectController::createProject($_POST['title'],
				(isset($_POST['description']) ? $_POST['description'] : NULL),
				$thumb_path,
				1);
			if ((!$nmd_port = getNMDPortfolio()) ||
				(!PortfolioController::addProjectToPortfolio($nmd_port->id(), $proj->id())))
			{
				$proj->delete();
				return permission_denied();
			}
			$id = $proj->id();
		}
	}
	// Handle thumbnail upload
	if ($_FILES['thumbnail']['name'] != '' )
	{
		// Get extention
		$ext = end( explode('.', $_FILES['thumbnail']['name']) );//substr(strrchr($_FILES['thumbnail']['name'], '.'), 1);
		$thumb_path = __DIR__ . $GLOBALS['thumbnail_path'] . $id . "." . $ext;
		$size = getimagesize($_FILES['thumbnail']['tmp_name']);
		$max_width = 100;
		$max_height = 100;
		if( file_exists($thumb_path) ) {
			unlink($thumb_path);			
		}
		if (//($size[0] > $max_width || $size[1] > $max_height) ||
			(!($ext == "jpg") && !($ext == "jpeg") && !($ext == "png") && !($ext == "gif")) ||
			(!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumb_path)))
		{
			$thumb_path = NULL;
		}
		else
		{
			$thumb_path = $GLOBALS['thumbnail_path'] . $id . "." . $ext;
		}
	}
	if (!ProjectController::editProject($id, 
		(isset($_POST['title']) ? $_POST['title'] : NULL),
		(isset($_POST['description']) ? $_POST['description'] : NULL),
		$thumb_path,
		NULL))
	{
		$app->flashNow('error', true);
	}
	return redirect('/project/'.$id);
});


/**
 *	Delete Project
 */
$app->get('/project/:id/delete', $authcheck_student, $submission_check, function($id) use ($app) {
	if ((!$proj = ProjectController::viewProject($id)) ||
		(!$proj->havePermissionOrHigher(OWNER)))
	{
		return permission_denied();
	}
	else
	{
		setBreadcrumb( array( 
				array(	'name'=>"New Media Portfolio",
						'url'=>'/portfolio'),
				array(	'name'=>$proj->title,
						'url'=>'/project/'.$id),
				));

		// Trim title if it is too long
		$t = substr($proj->title, 0, 50);
		if (strlen($t) < strlen($proj->title)) { $t = $t . "..."; }
		// Trim description if it is too long
		$desc = substr($proj->description, 0, 410);
		if (strlen($desc) < strlen($proj->description)) { $desc = $desc . "..."; }
		return $app->render('delete_project.html',
			array('project_id' => $id,
				'title' => $t,
				'description' => $desc,
				'thumbnail' => $proj->thumbnail));
	}
});

$app->post('/project/:id/delete', $authcheck_student, $submission_check, function($id) use ($app) {
	if ((!$proj = ProjectController::viewProject($id) ||
		(!$proj->havePermissionOrHigher(OWNER))) ||
		(!ProjectController::deleteProject($id)))
	{	// User does not have permission to edit this Project
		return permission_denied();
	}
	else
	{
		return redirect('/portfolio');
	}
});


/**
 *	Add Media
 */
$app->get('/project/:id/media/add', $authcheck_student, $submission_check, function($id) use ($app) {
	//TODO: Handle error messages from failed adds
	if ((!$proj = ProjectController::viewProject($id)) ||
		(!$proj->havePermissionOrHigher(OWNER)))
	{
		return permission_denied();
	}
	else
	{
		setBreadcrumb( array( 
				array(	'name'=>"New Media Portfolio",
						'url'=>'/portfolio'),
				array(	'name'=>$proj->title,
						'url'=>'/project/'.$id),
				));

		return $app->render('edit_media.html', 
			array('project_id' => $id,
				'media_id' => -1,
				'title' => "",
				'description' => "",
				'filename' => "",
				'mimetype' => ""));
	}
});


/**
 *	View Media
 */
$app->get('/project/:pid/media/:id', $authcheck_student, function($pid, $id) use ($app) {
	if (!$media = MediaController::viewMedia($id))
	{
		return permission_denied();
	}
	else
	{
		return $app->render('view_media.html', array('media' => $id));	//TODO: Pass content
	}
});


/**
 *	Edit Media
 */

function postMediaData($pid, $id) {
	if ((!$proj = ProjectController::viewProject($pid)) ||
		(!$proj->havePermissionOrHigher(OWNER)))
	{
		return permission_denied();
	}
	else
	{
		if ($id == -1)
		{	// Sent from add_media, we need to create a New Media (hehehe)
			if (!isset($_POST['title']))
			{	// Reject, form invalid
				$app->flash('error', true);
				return redirect('/project/'.$pid.'/media/add');	//TODO: Save partial fields on return to form
			}
			else
			{
				$media = MediaController::createMedia(
					(isset($_POST['file_content_type']) ? $_POST['file_content_type'] : NULL),
					$_POST['title'],
					(isset($_POST['description']) ? $_POST['description'] : NULL),
					(isset($_POST['file_name']) ? $_POST['file_name'] : NULL),
					(isset($_POST['file_size']) ? $_POST['file_size'] : NULL),
					(isset($_POST['file_md5']) ? $_POST['file_md5'] : NULL),
					(isset($_POST['file_name']) ? end(explode('.', $_POST['file_name'])) : NULL));
				ProjectController::addMediaToProject($proj->id(), $media->id());
				$id = $media->id();
			}
		}
		else	// We are editing an existing piece of Media
		{
			if (!MediaController::editMedia($id,
					(isset($_POST['file_content_type']) ? $_POST['file_content_type'] : NULL),
					(isset($_POST['title']) ? $_POST['title'] : NULL),
					(isset($_POST['description']) ? $_POST['description'] : NULL),
					(isset($_POST['file_name']) ? $_POST['file_name'] : NULL),
					(isset($_POST['file_size']) ? $_POST['file_size'] : NULL),
					(isset($_POST['file_md5']) ? $_POST['file_md5'] : NULL),
					(isset($_POST['file_name']) ? end(explode('.', $_POST['file_name'])) : NULL)))
			{
				return permission_denied();
			}
		}
		return redirect('/project/'.$pid);
	}
}

$app->post('/project/:pid/media/:id/edit', $authcheck_student, $submission_check, function($pid, $id) use ($app) {	
	postMediaData($pid, $id);
});


$app->get('/project/:pid/media/:id/edit', $authcheck_student, $submission_check, function($pid, $id) use ($app) {
	if( isset( $GLOBALS['specialUpload'] ) ) {
		$_POST = $GLOBALS['specialUpload']['uploadForm'];
		postMediaData($pid, $id);
	}

	if ((!$media = MediaController::viewMedia($id)) ||
		(!$media->havePermissionOrHigher(OWNER)) ||
		(!$project = ProjectController::viewProject($pid)) ||
		(!$project->havePermissionOrHigher(OWNER)))
	{
		return permission_denied();
	}
	else
	{
		setBreadcrumb( array( 
				array(	'name'=>"New Media Portfolio",
						'url'=>'/portfolio'),
				array(	'name'=>$project->title,
						'url'=>'/project/'.$pid.'/edit'),
				));

		return $app->render('edit_media.html', 
			array('media_id' => $id,
				'project_id' => $pid,
				'title' => $media->title,
				'description' => $media->description,
				'mimetype' => $media->mimetype,
				'filename' => $media->filename));
	}
});



/**
 *	Delete Media
 */
$app->get('/project/:pid/media/:id/delete', $authcheck_student, $submission_check, function($pid, $id) use ($app) {
	if ((!$media = MediaController::viewMedia($id)) ||
		(!$media->havePermissionOrHigher(OWNER)) ||
		(!$project = ProjectController::viewProject($pid)) ||
		(!$project->havePermissionOrHigher(OWNER)))
	{
		return permission_denied();
	}
	else
	{
		$app->render('delete_media.html', 
			array("media_id" => $id,
				"project_id" => $pid,
				"title" => $media->title,
				"description" => $media->description,
				"mimetype" => $media->mimetype,
				"filename" => $media->filename));
	}
});

$app->post('/project/:pid/media/:id/delete', $authcheck_student, $submission_check, function($pid, $id) use ($app) {
	if (!MediaController::deleteMedia($id))
	{
		return permission_denied();
	}
	else
	{
		return redirect('/project/'.$pid.'/edit');
	}
});


/**
 *	Review Portfolio
 */
// $app->get('/portfolio/review', $authcheck_student, function() use ($app) {					
// 	return $app->render('review_portfolio.html');		
// });


/**
 *	Portfolio Submission
 */
$app->get('/portfolio/submit', $authcheck_student, $submission_check, function() use ($app) {					
	return $app->render('submit_portfolio.html');		
});

$app->post('/portfolio/submit', $authcheck_student, $submission_check, function() use ($app) {
	$instance = getNMDAssignmentInstance();
	$port = getNMDPortfolio();
	if ($instance->submitWork($port->id(), true))
	{	// Success!
		return $app->render('portfolio_submitted.html');
	}
	else
	{	// Failue
		return permission_denied();
	}
});


/****************************************
 * Faculty Pages						*
 ***************************************/

/** Helper functions **/
function getNMDSubmittedPortfolios() {
	$instance = getNMDAssignmentInstance();

	$portfolios = array();
	$uid = AuthenticationController::get_current_user_id();
	foreach( $instance->children as $id=>$arr )
	{
		$port = Model::factory('Portfolio')->find_one($id);
		$student = $port->owner;
		$studentName = $student->first . ' ' . $student->last;
		$hasEvaluated = EvaluationAssignmentController::hasDoneEvaluation($uid, 1, $id);
		$portfolios[] = array('id'=>$id, 'student'=>$studentName, 'hasEvaluated'=> $hasEvaluated );
	}
	return $portfolios;
}

/**
 * View Portfolio Evaluations
 */
$app->get('/portfolios/evaluation-results', $authcheck_faculty, function() use ($app) {
	//Turn off initially

	$result = array();
	$portfolios = getNMDSubmittedPortfolios();

	//TODO: This should be based on permissions for the portfolio, not just faculty users
	$facultyMembers = array( array('name'=>'Mike', 'id'=>30),
						array('name'=>'Bill', 'id'=>27),
						array('name'=>'Owen', 'id'=>28),
						array('name'=>'Jon', 'id'=>26),
						array('name'=>'Joline', 'id'=>31),
						array('name'=>'Larry', 'id'=>29)
						);//Model::factory('User')->where('type_id', 1)->find_many(); 

	$gradeMapping = array('fail', 'discuss', 'pass');

	foreach( $portfolios as $portfolio )
	{
		$evaluations = array();
		$port_id = $portfolio['id'];
		foreach( $facultyMembers as $faculty ) {
			$evaluation = EvaluationAssignmentController::getEvaluationResults(1, $port_id, $faculty['id']);
			$grade = null;
			
			// Get grade for pass/discuss/fail question on portfolio evaluation
			if( $evaluation instanceOf Evaluation ) {
				$scores = $evaluation->scores;
				foreach( $scores as $score) {
					if( $score->component_id == 4 ) {
						$grade = $gradeMapping[$score->value - 1];
						break;
					}
				}
			}

			$evaluations[] = array('evaluator'=>$faculty['name'], 'grade'=>$grade);
		}
		//Get Student name
		$port = Model::factory('Portfolio')->find_one($port_id);
		$student = $port->owner;
		$studentName = $student->first . ' ' . $student->last;		

		setBreadcrumb( array( 
							array('name'=>"New Media Portfolios", 'url'=>'/portfolios')
						));

		$result[] = array('name'=>$studentName, 'portfolio_id'=>$port_id, 'facultyEvaluations'=>$evaluations );
	}
	
	//Get results


	return $app->render('view-evaluation-results.html', array('results' => $result) );
});

/**
 * Portfolio viewing 
 */
$app->get('/portfolios', $authcheck_faculty, function() use ($app) {

	$portfolios = getNMDSubmittedPortfolios();

	return $app->render('view_all_portfolios.html', array('portfolios' => $portfolios));
});

/**
 * View Particular Portfolio
 */
$app->get('/portfolios/:port_id', $authcheck_faculty, function($port_id) use ($app) {
	$port = Model::factory('Portfolio')->find_one($port_id);

	if ( !($port instanceOf Portfolio ) ) {
		return permission_denied();
	}

	// Create multi-dimensional array of Project properties
	$projects = array();
	foreach ($port->children as $child_id=>$arr)
	{
		$proj = Model::factory('Project')->find_one($child_id);	// assume all children are Projects
		// Trim title if it is too long
		$t = substr($proj->title, 0, 50);
		if (strlen($t) < strlen($proj->title)) { $t = $t . "..."; }
		// Trim description if it is too long
		$desc = substr($proj->description, 0, 410);
		if (strlen($desc) < strlen($proj->description)) { $desc = $desc . "..."; }
		$projects[] = array("project_id" => $proj->id(), "title" => $t, "description" => $desc, "thumbnail" => $proj->thumbnail, "type" => $proj->type);
	}

	$uid = AuthenticationController::get_current_user_id();
	$hasDoneEvaluation = EvaluationAssignmentController::hasDoneEvaluation($uid, 1, $port_id);

	$owner = $port->owner;
	$app->flashNow('isFaculty', true);
	$app->flashNow('port', array('id' => $port_id, 'owner_name' => $owner->first . " " . $owner->last));

	setBreadcrumb( array( 
						array('name'=>"New Media Portfolios", 'url'=>'/portfolios')
					));


	return $app->render('view_portfolio.html', 
		array('projects' => $projects,
			'hasDoneEvaluation'=>$hasDoneEvaluation));
});

/**
 * View Specific project within a portfolio
 */
$app->get('/portfolios/:port_id/project/:id', $authcheck_faculty, function($port_id, $id) use ($app) {
	$proj = Media::factory('Project')->find_one($id);

	if( !($proj instanceOf Project ) ) {
		return permission_denied();
	}
	
	$media = array();
	foreach ($proj->media as $media_id)
	{
		$m = Model::factory('Media')->find_one($media_id);
		$media[] = array('media_id' => $m->id(),
			'mimetype' => $m->mimetype,
			'title' => $m->title,
			'description' => $m->description,
			'created' => $m->created,
			'edited' => $m->edited,
			'filename' => $m->filename,
			'filesize' => $m->filesize,
			'md5' => $m->md5,
			'extension' => $m->extension);
	}

	$uid = AuthenticationController::get_current_user_id();
	$hasDoneEvaluation = EvaluationAssignmentController::hasDoneEvaluation($uid, 2, $id);

	setBreadcrumb( array( 
						array('name'=>"New Media Portfolios", 'url'=>'/portfolios'),
						array('name'=>$proj->owner->first . '\'s Portfolio', 'url'=>'/portfolios/' . $port_id)
					));


	$app->flashNow('isFaculty', true);
	return $app->render('view_project.html',
		array('portfolio_id' => $port_id,
			'project_id' => $proj->id(),
			'title' => $proj->title,
			'description' => $proj->description,
			'thumbnail' => $proj->thumbnail,
			'media_items' => $media,
			'hasDoneEvaluation'=>$hasDoneEvaluation));
});

/**
 * Evaluate a specific project
 */
$app->get('/portfolios/:port_id/project/:id/evaluate', $authcheck_faculty, function($port_id, $id) use ($app) {
	$proj = Model::factory('Project')->find_one($id);
	if( !($proj instanceOf Project ) ) {
		return permission_denied();
	}

	$components = FormController::buildQuiz(2);
	
	// Get student name
//	$student = $port->owner;
//	$studentName = $student->first . ' ' . $student->last;

	setBreadcrumb( array( 
						array('name'=>"New Media Portfolios", 'url'=>'/portfolios'),
						array('name'=>$proj->owner->first . '\'s Portfolio', 'url'=>'/portfolios/' . $port_id),
						array('name'=>'Project', 'url'=>'/portfolios/' . $port_id . '/project/' . $id)
					));


	$action_url = "/portfolios/" . $port_id . "/project/" . $id . '/evaluate';
	return $app->render('evaluation.html', 
		array('portfolioID'=>$port_id, 
			'name'=>'Project', 
			'components'=>$components, 
			'action_url'=>$action_url));

});

/**
 * View previous evaluation submission
 */
$app->get('/portfolios/:port_id/project/:id/view-evaluation', $authcheck_faculty, function($port_id, $id) use ($app) {
	$proj = Model::factory('Project')->find_one($id);
	if( !($proj instanceOf Project ) ) {
		return permission_denied();
	}


	$backURL = "/portfolios/" . $port_id . "/project/" . $id;
	$components = FormController::buildQuiz(2);


	$cuid = AuthenticationController::get_current_user_id();
	$evaluation = EvaluationAssignmentController::getEvaluationResults(2, $id, $cuid);
	$defaultValues = array();
	foreach( $evaluation->scores as $score) {
		$defaultValues[$score->component_id] = $score->value;
	}

	setBreadcrumb( array( 
						array('name'=>"New Media Portfolios", 'url'=>'/portfolios'),
						array('name'=>$proj->owner->first . '\'s Portfolio', 'url'=>'/portfolios/' . $port_id),
						array('name'=>'Project', 'url'=>'/portfolios/' . $port_id . '/project/' . $id)
					));


	return $app->render('view_evaluation.html',
		array('backURL'=>$backURL,
			'name'=>'Project',
			'defaultValues'=> $defaultValues,
			'components'=>$components));
});

/**
 * Submit Project evaluation results
 */
$app->post('/portfolios/:port_id/project/:id/evaluate', $authcheck_faculty, function($port_id, $id) use ($app) {
	//Ensure project exists to evaluate
	$proj = Model::factory('Project')->find_one($id);
	if( !($proj instanceOf Project ) ) {
		return permission_denied();
	}
	
	//Check if project has already been evaluated
	$uid = AuthenticationController::get_current_user_id();
	if( EvaluationAssignmentController::hasDoneEvaluation($uid, 2, $id) ) {
		$app->flash('message', 'Evaluation already submitted');
		redirect('/portfolios/' . $port_id . '/project/' . $id);
		$app->stop();
	}

	//Create Evaluation
	$evaluation = EvaluationController::createEvaluation(2, $id, $uid, 2);

	$result = EvaluationController::submitScores( $evaluation->id, $_POST );

	if ( !$result ) {
		//Rerender old page
		$app->flash('message', 'Required fields missing');
		$app->flash('defaultValues', $_POST); // Pass filled values back to page
		redirect('/portfolios/' . $port_id . '/project/' . $id . '/evaluate');
	}

	$app->flash('message', 'Evaluation successfully submitted.');
	redirect("/portfolios/" . $port_id . "/project/" . $id);
});

/**
 * Evaluate a specific portfolio
 */
$app->get('/portfolios/:port_id/evaluate', $authcheck_faculty, function($port_id) use ($app) {
	$port = Model::factory('Portfolio')->find_one($port_id);
	if( !($port instanceOf Portfolio ) ) {
		return permission_denied();
	}

	$components = FormController::buildQuiz(1);
	
	// Get student name
	$student = $port->owner;
	$studentName = $student->first . ' ' . $student->last;	

	setBreadcrumb( array( 
						array('name'=>"New Media Portfolios", 'url'=>'/portfolios'),
						array('name'=>$proj->owner->first . '\'s Portfolio', 'url'=>'/portfolios/' . $port_id)
					));


	$action_url = "/portfolios/" . $port_id . '/evaluate';
	return $app->render('evaluation.html', array('portfolioID'=>$port_id, 'name'=>$studentName . '\'s Portfolio', 'components'=>$components, 'action_url'=>$action_url));
});


/**
 * Submit Portfolio evaluation results
 */
$app->post('/portfolios/:port_id/evaluate', $authcheck_faculty, function($port_id) use ($app) {
	//Ensure project exists to evaluate
	$port = Model::factory('Portfolio')->find_one($port_id);
	if( !($port instanceOf Portfolio ) ) {
		return permission_denied();
	}

	//Check if portfolio has already been evaluated
	$uid = AuthenticationController::get_current_user_id();
	if( EvaluationAssignmentController::hasDoneEvaluation($uid, 1, $port_id) ) {
		$app->flash('message', 'Evaluation already submitted');
		redirect('/portfolios/' . $port_id);
		$app->stop();
	}
	
	//Create Evaluation
	$evaluation = EvaluationController::createEvaluation(1, $port_id, $uid, 1);
	
	$result = EvaluationController::submitScores( $evaluation->id, $_POST );

	if ( !$result ) {
		//Rerender old page
		$app->flash('message', 'Required fields missing');
		$app->flash('defaultValues', $_POST); // Pass filled values back to page
		redirect('/portfolios/' . $port_id . '/evaluate');
		$app->stop();
	}

	$app->flash('message', 'Evaluation successfully submitted.');
	redirect("/portfolios/" . $port_id);
});


/**
 * View previous evaluation submission
 */
$app->get('/portfolios/:port_id/view-evaluation', $authcheck_faculty, function($port_id) use ($app) {
	$port = Model::factory('Portfolio')->find_one($port_id);
	if( !($port instanceOf Portfolio ) ) {
		return permission_denied();
	}

	$backURL = "/portfolios/" . $port_id;
	$components = FormController::buildQuiz(1);


	$cuid = AuthenticationController::get_current_user_id();
	$evaluation = EvaluationAssignmentController::getEvaluationResults(1, $port_id, $cuid);
	$defaultValues = array();
	foreach( $evaluation->scores as $score) {
		$defaultValues[$score->component_id] = $score->value;
	}

	setBreadcrumb( array( 
						array('name'=>"New Media Portfolios", 'url'=>'/portfolios'),
						array('name'=>$proj->owner->first . '\'s Portfolio', 'url'=>'/portfolios/' . $port_id)
					));

	return $app->render('view_evaluation.html',
		array('backURL'=>$backURL,
			'name'=>'Project',
			'defaultValues'=> $defaultValues,
			'components'=>$components));
});

// RUN THE THING
$app->run();



