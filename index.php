<?php

require_once 'libraries/Slim/Slim.php';
//require_once 'libraries/Views/TwigView.php';

require_once 'libraries/Paris/idiorm.php';
require_once 'libraries/Paris/paris.php';

//require_once 'models/Evaluation.php';

require_once 'libraries/settings.php';
require_once 'libraries/core.php';
//require_once 'libs/firstclassAuth.php';

// Configuration
ORM::configure("mysql:host=$HOST;dbname=$DATABASE");
ORM::configure('username', $USERNAME);
ORM::configure('password', $PASSWORD);

// Start Slim
$app = new Slim(array(
	//'view' => new TwigView,
));


// Authentication Check
$authCheck = function() use ($app) {
	//$user = check_ses_vars();		
	if( !$user ) {
		redirect('/login');
	} else {

		//make sure user is valid
		$myself = Users::myself();
		if ( ! $myself instanceof Users ) {
			redirect('/login');
		}
	}
	return true;
};		

/** System Home */
$app->get('/', $authCheck, function() use ($app) {
	echo "Hello World";
});



$app->run();

