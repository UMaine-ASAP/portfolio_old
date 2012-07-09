<?PHP
/************************************************
 * ROUTING!!									*
 ***********************************************/

// Inform app of the web root for the next HTML request
$app->flashNow('web_root', $GLOBALS['web_root']);


/**
 *	Webroot
 */
$app->get('/', $authcheck_student, function() use ($app) {
	return redirect($GLOBALS['web_root']);
});


/**
 *	Login
 */
$app->get('/login', $redirect_loggedInUser, function() use ($app) {
	return $app->render('login.html');
});

$app->post('/login', function() use ($app) {
	if (isset($_POST['username']) && isset($_POST['password']) &&
		AuthenticationController::attemptLogin($_POST['username'], $_POST['password']))
	{	// Success!
		return redirect($GLOBALS['web_root']);
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
$app->get('/logout', $authcheck_student, function() use ($app) {
	AuthenticationController::logOut();
	$app->flash('header', 'You have been successfully logged out.');
	return redirect('/login');
});


/**
 *	Register
 */
$app->get('/register', $redirect_loggedInUser, function() use ($app) {					
	return $app->render('register.html');
});

$app->post('/register', function() use ($app) {
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
				AuthenticationController::attemptLogin($_POST['username'], $_POST['password']);
				// Create User's NMD portfolio
				$port = PortfolioController::createPortfolio("New Media Freshman Portfolio 2012", "New Media Freshman Portfolio 2012", 1);
				// Add permission for User to submit to NMD 2012 AssignmentInstance
				$instance = getNMDAssignmentInstance();
				$instance->addPermissionForUser($user->id(), SUBMIT);
				return redirect($GLOBALS['web_root']);
			}
		}
});


/**
 *	View Portfolio
 */
$app->get($GLOBALS['web_root'], $authcheck_student, function() use ($app) {
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
			setBreadcrumb( array( 
				array(	'name'=>"New Media Portfolio", 
						'url'=>$GLOBALS['web_root'])
				));
		
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
						'url'=>$GLOBALS['web_root']),
				array ('name' => 'View Project',
						'url' => '/project/' . $id)
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
						'url'=>$GLOBALS['web_root']),
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
						'url'=>$GLOBALS['web_root']),
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
		return redirect($GLOBALS['web_root']);
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
						'url'=>$GLOBALS['web_root']),
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
						'url'=>$GLOBALS['web_root']),
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

?>