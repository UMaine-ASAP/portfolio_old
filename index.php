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




$projects = array(
		array('id'=>0, 'name'  => 'Proposal 1: Wall-mounted Chadbourne screen',
		'goal'        => 'Correll Goal #1 (Support Center)',
		'cost'        => '$3000',
		'description' => 'This would be set up in the hallway outside Still Water or related space to connect IMRC and Chadbourne Hall virtually, possibly including some modification to existing walls.'),

	array('id'=>1, 'name'  => 'Proposal 2: Second phase of online tutorials',
	'goal'        => 'Correll Goal #2 (Sharing/p2p learning)',
	'cost'        => '$2760',
	'description' => '<ul><li>Additional ScreenFlow licenses ($100 x 5)</li>
	<li>18 commissioned, high-quality screencasts ($80 each)</li>
	<li>18 sets of supplementary documentation--scripts. quizzes, badges, Pool entries--to go with each screencast ($40 each)</li>
	<li>CodeSchool 4-month license ($100)</li></ul>'),

		array('id'=>2, 'name'  => 'Proposal 3: Special mobile devices for testing',
		'goal'        => 'Correll Goal #3 (Leadership/tools)',
		'cost'        => '$730',
		'description' => 'For testing mobile development with PhoneGap, iOS, etc.
iPad Mini ($330)
Nexus 10 ($400)
'),

		array('id'=>3, 'name'  => 'Proposal 4: Speakers for Digital Humanities Week II in fall 2013',
		'goal'        => 'Correll Goal #6 (Culture)',
		'cost'        => '$1000',
		'description' => 'Funds would be supported by 3-1 match from UMaine Humanities Initiative, plus labor from Christopher Ohge, Postdoctoral Fellow in Digital Humanities'),

		array('id'=>4, 'name'  => 'Proposal 5: Social media recruitment efforts',
		'goal'        => 'Correll Goal #7 (Recruitment)',
		'cost'        => '$5000',
		'description' => 'Faculty time (one-course buyout) to revamp department Web site, launch upgraded Facebook page, email campaign to connect NMD prospectives, undergrads, alumni.'),

		array('id'=>5, 'name'  => 'Proposal 6: Upgraded student Web host',
		'goal'        => 'Correll Goal #14 (Career/portfolio)',
		'cost'        => '$550',
		'description' => '
		<ul><li>SSL certificate for 3 years (15 + 4*12)*3</li>
		<li>Additional DreamHost account to address overload for 3 years ($120 x 3)</li></ul>'),

		array('id'=>6, 'name'  => 'Proposal 7',
		'goal'        => 'Correll Goal #3 Cutting Edge Tools',
		'cost'        => '$12,500',
		'description' => '<p>To provide access to cutting edge tools important for new media and related fields (such as industrial design) and plan for the maintenance and upgrading of these tools indefinitely.</p>
		<p>Multi-touch interactive tables and walls are becoming increasingly affordable. Previously in the Multi-touch programing class spent the majority of time researching and building such devises. Although a learning experience in itself it has not allowed students to focus on the actual development of multi-touch applications that can explore new forms of interaction, collaboration, social interaction, implications on learning and the displaying and manipulation of data and information.</p>
		<p>The Presenter touch wall is a 65” Active 3D LED LCD HD multi-touch display supporting 32+ touch points. The device can be used as an interactive wall or in a horizontal position as an interactive table</p>'),

		array('id'=>7, 'name'  => 'Proposal 8 Request - set up a fund of 10K to support new tools and technologies for New Media need at the IMRC ',
		'goal'        => '',
		'cost'        => '',
		'description' => ''),

		array('id'=>8, 'name'  => 'Proposal 9: Researcher in Residence Program for IMRC - Creative Technologists, Intermedia Artists and New Media Producers. ',
		'goal'        => '',
		'cost'        => '',
		'description' => ''),

		array('id'=>9, 'name'  => 'Proposal 10: Student Research Grants',
		'goal'        => 'Correll Goal #4 (Support)',
		'cost'        => '$1500',
		'description' => 'This amount funds about 4-8 grants (nano grants between $9-99, micro grants between $100-499). These grants support development of student research and projects in and outside of the classroom. Materials or technology purchased by grants belongs to the department and returns to it when students graduate for use by other students. Grant forms developed by the New Media Society have been sent to faculty. Selection is by peer review by either the New Media Society, or NMD core classes (206/306) and is done on a rolling basis.'),

		array('id'=>10, 'name'  => 'Proposal 11: iPad 10 pack, airport express, apple TV  (for wireless projecting in classes)',
		'goal'        => 'Correll Goal #3 (Cutting Edge Tools) #4 (Support), #10 (Training)',
		'cost'        => '$5379',
		'description' => 'We have had 2 classes thus far with dedicated iPad use, Bill\'s IMD class and Joline\'s NMD 443, and in addition various request for short term usage by courses like NMD 104. The Apple TV and Apple router connect the iPads to the overhead projector for viewing work development. The devices allow classes to work with authoring environments like iBooks Author, perform as cameras and video input devices, and also support programming-- widgets can be developed using HTML5 thus furthering dept base language competence, and marketable skills.')







// // proposal 8
//  Goal #4 - Support student research and creativity and inventive application of new media thinking and applications.
// By providing state of the art tools and technologies through the IMRC the needs of the students for research and creativity activities will be better supported. This will allow us to meet new and changing needs as they develop and as we set priorities in the new facilities.
// •	
// Goal #7 - Attract a stronger, more diverse, and more socially and professionally engaged student body to New Media.
// Having state of the art tools and technologies will directly support / maintain a strong and attractive program for top level students
// •	
// Goal #8 - Increase student retention and satisfaction (with the program, its structure and opportunities in the program).
// New media focused tools and technologies will support greater student satisfaction, more opportunities and should positively benefit students retention.
// •	
// Goal #10 - Increase exposure to, and training on, tools and technologies relevant for new media work/production (hands-on and virtual applied training).
// Providing selected tools and technologies will support an increase the diversity and type of technology training and hands on learning available to students. This program will also us to respond to student needs and changing technologies which will in turn create new and divergent opportunities for students and  support developing hand on training and a better, more responsive engagement in the teaching and learning experiences of students.



// // Proposal 9
// Request:
// Four thousand per year of Correll Funds to be used to support a Researcher in Residence Program in New Media at the University of Maine.
// Synopsis:
// Two scholars, researchers or creative producers will be brought in each year (one in NMD and one in IMD) to work in residence at the Innovative Media, Research and Commercialization Center. Selected through an international open call or direct invitations, these residencies will last between 6 and 12 weeks depending on the individual and the projects in which they are engaged. While at the IMRC each Fellow will be involved in many of the following: teach an intensive class and/or conduct workshops, give a public lecture on their research or creative production, work directly with students on their projects, hold open lab hours, and interact daily with students and faculty at the IMRC Center. The residency is intended to support a period of concentrated work and immersion in creative investigations, cutting-edge research, and production of visionary, experimental applications, projects and artworks. 
// Duration/ frequency: 
// The program’s term is varied but there are two yearly awardees one in the Spring and one in the Fall and the duration is for a period of between 8 and 12 weeks each. Researchers in Residence will be selected from an open call, based on the quality of the work or research being proposed, the availability of the necessary tools and skills to support the work, and the relation of the proposed project to the overarching research agenda and activities of the New Media Department, the IMRC Center, the Foster Student Innovation Center and/or the University of Maine. In specific one awardee will be supported by NMD and thus more specific to New Media ideas, tools and technologies and one will be supported by IMD and thus the selected researcher might work more broadly or diversely than New Media methods, tools and technologies.
// Award: 
// RRP awardees are granted a $5,000 stipend, studio space, 24/7 access to the IMRC’s state of the art digital design, media production, prototyping and fabrication studios, a materials budget of $3,000, and some support for travel to the IMRC Center in Orono.
// Candidates:
// The ideal Research in Residence will both contribute to, and benefit from, the collective environment at the IMRC Center and the affiliated programs in New Media and Intermedia and will embrace the spirit of interdisciplinarity shared across the University of Maine. To promote collaboration and the sharing of diverse skill sets, these programs are grounded in and encourage interdisciplinary research in hybrid applications, innovative problem solving, and their utilization through the full range of research, development and commercialization. The aim of this program is threefold: to foster and support innovative Research in New Media, Intermedia and other creative fields at the University of Maine; to build on the University of Maine’s reputation as a center of excellence; and to bring together students and faculty working at the NMIRDC with expert external participants to create a collaborative and innovative research/creative environment.
// During the course of the residency RRP awardees are expected to participate in, and lead, public events including workshops, demonstrations of research in progress, panel discussions, and other such program and public presentations. In addition to these type of events RRP awardees will be expected to lead a one to two week intensive course for graduates students and advanced undergraduates (in Intermedia and New Media) on some aspect of advanced research, development or production related to their own work, interests and skill sets.
// Key Benefits of the Researchers in Residence Program and connections to New Media Departmental Correll funds goals:
// Although this proposed program can and does connect to all of the New Media Goals some are more deeply and specifically connected to the nature and aims of the Researcher in Residence Program, and these are:

//  Goal #4 - Support student research and creativity and inventive application of new media thinking and applications.
// By bring in researchers in New Media and related areas this will support and model research as an ongoing practice for students. Also the RR awardees will be interacting and work with students on a regular basis on the student’s own creative and research activities.
// Goal #6 - Create a vibrant "culture" of teaching and learning that is exciting and energetic in its pursuit of new knowledge and engages students and faculty through both virtual and face-to-face experiences.
// Having a strong group of visiting creative producers and researchers working in the IMRC Center for 8-12 weeks will ramp up an intensity of activity in the facility and thus help to establish a stronger culture of learning, creative applications and work.
// Goal #7 - Attract a stronger, more diverse, and more socially and professionally engaged student body to New Media.
// Having a strong program of visiting producers and researchers and the classes, workshops and lectures that they will present will create an attractive program for top level students and by the variety of the visiting Researchers in Residence it will help bring more diversity and professionally engagement to UMaine.
// Goal #8 - Increase student retention and satisfaction (with the program, its structure and opportunities in the program).
// Exciting and diverse programs with national and international level connections will support greater student satisfaction, more opportunities and should positively benefit students retention.
// Goal #9 - Increase student and faculty exposure to new media artists, journalists, technologists, entrepreneurs, designers and producers.
// This will directly do this by bring an increased variety of people to campus for students and faculty to interact with. This will also dovetail nicely with the stated mission of  making “UMaine New Media a world class center for blended theoretical and applied learning.”
// Goal #10 - Increase exposure to, and training on, tools and technologies relevant for new media work/production (hands-on and virtial applied training).
// The associated classes and workshops offered by the RR awardees will increase the diversity and type of technology training and hands on learning available to students and it will directly contribute to the mentorship opportunities for students and offering a deep engagement in the teaching and learning experiences of students.


// Other benefits of the RR Program
// - Increase new and innovative RD and C supported by the IMRC Center, New Media and Intermedia
// - Bring in a rotating group of advanced practitioners as a means of keeping the new in the New Media initiatives at UMaine
// - Build a cadre of leading research and creative producers with ties to UMaine.
// - Establish a mentor system of leading edge researchers in New Media and Intermedia
//  - Originate new opportunities for students to build skills and broaden their knowledge of diverse research/creative initiatives
// - Support current research in the state of Maine and bring in new initiatives in the fields of Art, New Media and Intermedia.


// General budget breakdown (per residency, so x2 9500):
// Stipend							5,000.
// Material Budget						3,000.
// Travel to and from Orono					1,000.
// Events and general support					   500.
// Total yearly cost: 19,000.00

// Funding model/sources for prioritized NMD RR:
// Teach intensive class (8-12 weeks)	 	   		   3500.
// Guest lecture stipend				 	   1000.
// CLADS match of 1000.00 					   1000.	
// NMD Correll Contribution				   	   4000.
// Total 								   9500.




	);

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
	//return true;
	/*return true;

	$instance = getNMDAssignmentInstance();
	$port = getNMDPortfolio();
	foreach ($instance->children as $child_id=>$arr)
	{
		if ($child_id == $port->id())
		{
			return true;
		}
	}*/
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
 * Portfolio viewing 
 */
$app->get('/portfolios', $authcheck_faculty, function() use ($app) {

	$portfolios = $GLOBALS['projects'];
	$uid = AuthenticationController::get_current_user_id();	
	foreach( $portfolios as &$portfolio)
	{

		$portfolio['hasEvaluated'] = EvaluationAssignmentController::hasDoneEvaluation($uid, 1, $portfolio['id']) ;

	}	


	return $app->render('newmedia-project-review/projects.twig', array('portfolios' => $portfolios));
});



/**
 * View Portfolio Evaluations
 */
$app->get('/portfolios/evaluation-results', $authcheck_faculty, function() use ($app) {
	//Turn off initially
	//return permission_denied();

	$result = array();
	$portfolios = $GLOBALS['projects'];

	//TODO: This should be based on permissions for the portfolio, not just faculty users
	$facultyMembers = array( array('name'=>'Mike', 'id'=>65),
						array('name'=>'Bill', 'id'=>67),
						array('name'=>'Owen', 'id'=>69),
						array('name'=>'Jon', 'id'=>68),
						array('name'=>'Joline', 'id'=>64),
						array('name'=>'Larry', 'id'=>66)
						);//Model::factory('User')->where('type_id', 1)->find_many(); 


	foreach( $portfolios as $portfolio )
	{
		$evaluations = array();
		$port_id = $portfolio['id'];
		$avg = 0;
		foreach( $facultyMembers as $faculty ) {
			$evaluation = EvaluationAssignmentController::getEvaluationResults(1, $port_id, $faculty['id']);
			$grade = null;
			// Get grade for pass/discuss/fail question on portfolio evaluation
			if( $evaluation instanceOf Evaluation ) {
				$scores = $evaluation->scores;
				foreach( $scores as $score) {
					if( $score->component_id == 4 ) {
						$grade = $score->value;
						$avg += $grade;
						break;
					}
				}
			}

			$evaluations[] = array('evaluator'=>$faculty['name'], 'grade'=>$grade);
		}
		$evaluations[] = array('evaluator'=>'average', 'grade'=> round($avg / count($facultyMembers), 2));

		//Get Student name
		$port = Model::factory('Portfolio')->find_one($port_id);

		setBreadcrumb( array( 
							array('name'=>"New Media Projects", 'url'=>'/portfolios')
						));

		$result[] = array('name'=>'Project ' . ($port_id+1), 'portfolio_id'=>$port_id, 'facultyEvaluations'=>$evaluations );
	}
	
	//Get results


	return $app->render('newmedia-project-review/view-evaluation-results.twig', array('results' => $result) );
});

/**
 * Portfolio viewing 
 */
$app->get('/projects', $authcheck_faculty, function() use ($app) {

	$portfolios = getNMDSubmittedPortfolios();


	return $app->render('view_all_portfolios.html', array('portfolios' => $portfolios));
});

/**
 * View Particular Portfolio
 */
$app->get('/portfolios/:port_id', $authcheck_faculty, function($port_id) use ($app) {
	$project = $GLOBALS['projects'][$port_id];
	$uid = AuthenticationController::get_current_user_id();	
	$hasDoneEvaluation = EvaluationAssignmentController::hasDoneEvaluation($uid, 1, $port_id) ;

	return $app->render('newmedia-project-review/project.twig', 
		array('project' => $project, 'hasDoneEvaluation'=>$hasDoneEvaluation));
});

/**
 * Evaluate a specific portfolio
 */
$app->get('/portfolios/:port_id/evaluate', $authcheck_faculty, function($port_id) use ($app) {
	//Ensure project exists to evaluate
	$project = $GLOBALS['projects'][$port_id];

	$components = FormController::buildQuiz(1);
	// Get student name

	setBreadcrumb( array( 
						array('name'=>"New Media Projects", 'url'=>'/portfolios'),
						array('name'=>'Project', 'url'=>'/portfolios/' . $port_id)
					));


	$action_url = "/portfolios/" . $port_id . '/evaluate';
	return $app->render('evaluation.html', array('portfolioID'=>$port_id, 'name'=>$project['name'], 'components'=>$components, 'action_url'=>$action_url));
});


/**
 * Submit Portfolio evaluation results
 */
$app->post('/portfolios/:port_id/evaluate', $authcheck_faculty, function($port_id) use ($app) {
	//Ensure project exists to evaluate
	$project = $GLOBALS['projects'][$port_id];

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
	$port = $GLOBALS['projects'][$port_id];



	$backURL = "/portfolios/" . $port_id;
	$components = FormController::buildQuiz(1);


	$cuid = AuthenticationController::get_current_user_id();
	$evaluation = EvaluationAssignmentController::getEvaluationResults(1, $port_id, $cuid);
	$defaultValues = array();
	foreach( $evaluation->scores as $score) {
		$defaultValues[$score->component_id] = $score->value;
	}

	setBreadcrumb( array( 
						array('name'=>"New Media Projects", 'url'=>'/portfolios'),
						array('name'=>'Project', 'url'=>'/portfolios/' . $port_id)
					));

	return $app->render('view_evaluation.html',
		array('backURL'=>$backURL,
			'name'=>'Project',
			'defaultValues'=> $defaultValues,
			'components'=>$components));
});

// RUN THE THING
$app->run();



