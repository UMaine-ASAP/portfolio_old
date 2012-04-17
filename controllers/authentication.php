<?php
require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/phpass-0.3/PasswordHash.php');
require_once('controllers/user.php');
require_once('models/mappings.php');
require_once('models/accesslevel.php');

class AuthenticationController
{
	/**
	 * Gets the user who is currently logged in. Will log the user out of their UserID does not belong
	 * to any known user. Currently, this seems pretty vulnerable to anyone who feels like changing their
	 * UserID.
	 *		@return The logged in user as an ORM object, or false if no user is logged in.
	 */
	static function getCurrentUser()
	{
		//change this to true if you want to add a session_timeout in $GLOBALS
		//and log users out after that period of time if they've been inactive
		$logOutOnLastAccess = false;

		if(isset($_SESSION['UserID']))
		{
			//update the user's access time
			$latestAccess = time();

			if($logOutOnLastAccess && $latestAccess - $_SESSION['LastAccess'] > $GLOBALS["session_timeout"])
			{
				self::logout();
				return false;
			}

			$_SESSION['LastAccess'] = $latestAccess;

			//try to acquire a user object from Paris; assuming that UserID holds the user's ID field
			$user = UserController::getUser(intval($_SESSION['UserID']));

			if ($user === false) //invalid session ID
			{
				//clear the bad session info
				self::logOut();
				return false;
			}

			return $user;
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
	static function attemptLogin($username, $password)
	{
		//sanity check -- if a user attempts to log in and they/another user is actually logged in, log them out first
		if (self::isLoggedIn())
		{
			self::logOut();
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
			self::doLogin($user);
			return true;
		}

		//nope
		return false;
	}

	/**
	 * Completely destroys the current session.
	 */
	static function destroySession()
	{
		$_SESSION = array();
		session_destroy();
	}

	/**
	 *	Attempts to hash a specified plaintext password. If successful, returns the hash to the caller.
	 *	Hashing will fail if the password is longer than 72 characters (to prevent DDOS) or if the resulting
	 *	hash is less than 20 characters long (which means something went wrong while creating the hash)
	 *
	 *		@param string $password The plaintext password to hash.
	 *	@return The hashed password, or false if hashing failed.
	 */
	static function createHash($password)
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
	 *		@param int $userid The ID of the user whose password will be updated.
	 *		@param string $password The plaintext of the user's new password (will be hashed)
	 *
	 *	@return True if the update was successful, false otherwise.
	 *
	 */
	static function updateUserPassword($userid, $password)
	{
		if ($hash = self::createHash($password))
		{
			if ($user = UserController::getUser($userid))
			{
				$user->pass = $hash;
				return $user->save();
			}
		}

		return false;
	}

	/**
	 * Logs the current user out. Currently, it just destroys their session. In the future, it'll
	 * probably have to take more factors into account.
	 */
	static function logOut()
	{
		self::destroySession();
	}

	/**
	 * Determines if a user is logged in.
	 *	@return True if a user is currently logged in, false otherwise.
	 */
	static function isLoggedIn()
	{
		return (self::getCurrentUser() !== false);
	}

	/**
	 * Resets $_SESSION, then sets appropriate variables within it (currently UserID and LastAccess)
	 */
	private static function doLogin($user)
	{
		$_SESSION = array();
		$_SESSION['UserID'] = $user->user_id;
		$_SESSION['LastAccess'] = time();
	}
}