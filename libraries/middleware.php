<?PHP

/****************************************
 * MIDDLEWARE FUNCTIONS					*
 ***************************************/

/** 
 * Middleware to check student authentication and redirect to login page 
 */
$authcheck_student = function () use ($app)
{	
	//Redirect to login if not authenticated
	if ( ! AuthenticationController::checkLogin() )
	{
		$app->flashNow('logged_in', false);
		redirect('/login');
		return false;
	}
	else
	{
		$app->flashNow('logged_in', true);
		$app->flashNow('portfolioIsSubmitted', portfolioIsSubmitted() );
		return true;
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
 * Middleware to redirect user based on role to role-specific homepage
 */
$redirect_loggedInUser = function () use ($authcheck_student)
{
	//@TODO: We will want to get the user role and direct user depending on whether student or faculty ...

	if ( AuthenticationController::checkLogin() )
	{	// User is already logged in
		return redirect($GLOBALS['web_root']);
	}
};

?>