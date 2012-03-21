<?php


/**
 * isLoggedIn
 * 
 * Returns true if user is logged in, false otherwise
 */
function isLoggedIn() {
	return (getLoggedInUser() !== 0);
}

/**
 * getLoggedInUser
 * 
 * Check if user is logged in
 * @TODO: 0 is used to denote not logged in. What if user has id 0 in database?
 */
function getLoggedInUser() {
	
	// Check session variables
	if(isset($_SESSION['UserID']) && isset($_SESSION['LastAccess'])) {
		$latestAccess = time();

		// Check access time
		if($latestAccess - $_SESSION['LastAccess'] > $GLOBALS["session_timeout"]) {
			user_logout();
			return 0;
		}

		// Store most recent access
		$_SESSION['LastAccess'] = $latestAccess;

		return $_SESSION['UserID'];
	}
	return 0;
}

/**
* Login
* 
* Authenticates user to system
**/
function login($userID) {
	// @NOTE: we don't call start_session() since Slim already started the session
	$_SESSION['UserID'] = $UserID;
	$_SESSION['LastAccess'] = time();
}

/**
 * Logout
 * 
 * Removes user's authentication
 */
function logout() {
	// Destroy session
	session_unset();
	unset($_SESSION['UserID']);
	unset($_SESSION['LastAccess']);
}
