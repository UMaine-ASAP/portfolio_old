<?php
require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/phpass-0.3/PasswordHash.php');
require_once('controllers/user.php');
require_once('models/mappings.php');
require_once('models/accesslevel.php');

/**
 * Controller handling all matters auth
 *
 * @package Controllers
 */
class AuthenticationController
{
	/**
	 * Gets the ID of the User who is currently logged in. Since it calls check_login, it will log the user
	 * out if their session has expired or their IP has changed during their current session.
	 *
	 *		@return The logged in user's ID, or false if no user is logged in.
	 */
	static function get_current_user_id()
	{
		if (self::check_login())
		{
			return intval($_SESSION['UserID']);
		}

		return false;
	}

	/**
	 * Determines if a user is currently logged in. If so, it checks their session data and logs them out
	 * if their session has expired or their IP has changed. It then updates their latest access time to
	 * the current time() value
	 *
	 *	@return True if a user is currently logged in, false otherwise
	 */
	static function check_login()
	{
		if(isset($_SESSION['UserID']))
		{
			//$latestAccess = time();

			//if ($latestAccess - $_SESSION['LastAccess'] > $GLOBALS["session_timeout"])
			//{
			//	self::log_out();
			//	return false;
			//}

			//if (strcmp($_SESSION['RemoteIP'], $_SERVER['REMOTE_ADDR']) != 0)
			//{
			//	self::log_out();
			//	return false;
			//}

			//$_SESSION['LastAccess'] = $latestAccess;
			return true;
		}

		return false;
	}

	/**
	 *	Attempts to log in as a user with the given username and password. If the login is successful,
	 *	the appropriate $_SESSION variables will automatically be set. Note that this function takes
	 *	an arbitrary non-insignificant amount of time to return a result; this is intentional. Not only
	 *	does it make brute forcing harder, it prevents some kind of theoretical attacker from timing
	 *	it and determining whether they have a valid username.
	 *
	 *		@param string $username The username given by the user attempting to log in.
	 *		@param string $password The plaintext password submitted by the user attempting to log in.
	 *
	 *	@return False if the login was unsuccessful, true otherwise.
	 */
	static function attempt_login($username, $password)
	{
		//sanity check -- if a user attempts to log in and they/another user is actually logged in, log them out first
		if (self::is_logged_in())
		{
			self::log_out();
		}

		//passwords greater than 72 characters in length take a long time to hash; by blatantly disallowing them we keep
		//people from putting in gigantic passwords and ddosing the website (intentionally or accidentally)
		if (strlen($password) > 72)
		{
			return false;
		}

		//try to find a user with that username
		if (!$user = Model::factory('User')->where('username', $username)->find_one())
		{
			usleep(rand(100000, 250000)); //so that they can't easily search for usernames by timing how long this takes
			return false;
		}

		//instantiate a PasswordHash class from phpass
		$hasher = new PasswordHash(8, false);

		if ($hasher->CheckPassword($password, $user->pass))
		{
			//great success!
			self::do_login($user);
			return true;
		}

		//nope
		return false;
	}

	/**
	 * Completely destroys the current session.
	 */
	static function destroy_session()
	{
		if (session_id() === '')
		{
			$_SESSION = array();
			return true;
		}

		$_SESSION = array();
		session_destroy();
		return true;
	}

	/**
	 *	Attempts to hash a specified plaintext password. If successful, returns the hash to the caller.
	 *	Hashing will fail if the password is longer than 72 characters (to prevent DDOS) or if the resulting
	 *	hash is less than 20 characters long (which means something went wrong while creating the hash)
	 *
	 *		@param string $password The plaintext password to hash.
	 *	@return The hashed password, or false if hashing failed.
	 */
	static function create_hash($password)
	{
		$hasher = new PasswordHash(8, false);

		if (strlen($password) > 72)
		{
			//passwords longer than 72 characters can take a VERY LONG time to hash
			return false;
		}

		$hash = $hasher->HashPassword($password);

		if (strlen($hash) > 20)
		{
			return $hash;
		}

		return false; //if strlen was less than 20, something was direly wrong
	}

	/**
	 * Sets a users's password to the secure hash of the plaintext password passed to the function.
	 *		@param int $userID The ID of the user whose password will be updated.
	 *		@param string $password The plaintext of the user's new password (will be hashed)
	 *
	 *	@return True if the update was successful, false otherwise.
	 *
	 */
	static function update_user_password($userID, $password)
	{
		// Check user has permissions to change password
		if (!$user = self::get_current_user_id())
		{
			return false;
		}

		if ($hash = self::create_hash($password))
		{
			return UserController::editUser($userID, NULL, $password);
		}

		return false;
	}

	/**
	 * Logs the current user out. Currently, it just destroys their session. In the future, it'll
	 * probably have to take more factors into account.
	 */
	static function log_out()
	{
		self::destroy_session();
	}

	/**
	 * Determines if a user is logged in.
	 *	@return True if a user is currently logged in, false otherwise.
	 */
	static function is_logged_in()
	{
		return self::check_login();
	}

	/**
	 * Resets $_SESSION, then sets appropriate variables within it (currently UserID and LastAccess)
	 */
	private static function do_login($user)
	{
		$_SESSION = array();
		$_SESSION['UserID'] = $user->user_id;
		$_SESSION['LastAccess'] = time();
		$_SESSION['RemoteIP'] = $_SERVER['REMOTE_ADDR'];
	}
}
