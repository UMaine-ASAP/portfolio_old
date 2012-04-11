<?php
require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/phpass-0.3/PasswordHash.php');
require_once('controllers/user.php');

class AuthenticationController
{
	/**
	 * Gets the user who is currently logged in.
	 *		@return The logged in user as an ORM object, or false if the user is not logged in.
	 */
	static function getCurrentUser()
	{
		if(isset($_SESSION['UserID']) && isset($_SESSION['LastAccess']))
		{
			$latestAccess = time();

			if($latestAccess - $_SESSION['LastAccess'] > $GLOBALS["session_timeout"])
			{
				user_logout();
				return false;
			}

			$_SESSION['LastAccess'] = $latestAccess;
			$user = UserController::getUser(intval($_SESSION['UserID']));

			assert($user !== false);
			return $user;
		}

		return false;
	}

	static function attemptLogin($username, $password)
	{
		if (strlen($password) > 72)
		{
			return false; //ddos prevention
		}

		if (!$user = Model::factory('User')->where('username', $username)->find_one())
		{
			usleep(rand(100000, 250000)); //so that they can't easily search for usernames
			return false;
		}

		echo 'Trying to log in as ' . $user->username . '<br />';
		echo "User's password:" . $user->pass . ' <br />';

		$hasher = new PasswordHash(8, false);

		if ($hasher->CheckPassword($password, $user->pass))
		{
			self::doLogin($user);
			return true;
		}

		return false;
	}

	private static function doLogin($user)
	{
		$_SESSION['UserID'] = $user->user_id;
		$_SESSION['LastAccess'] = time();
	}

	static function destroySession()
	{
		$_SESSION = array();
		session_destroy();
	}

	static function createHash($password)
	{
		$hasher = new PasswordHash(8, false);

		if (strlen($password) > 72)
		{
			//we can't allow this; it'll effectively ddos us
			return false;
		}

		$hash = $hasher->HashPassword($password);

		if (strlen($hash) > 20)
		{
			return $hash;
		}

		return false; //if strlen was less than 20, something was direly wrong
	}

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

	static function logOut()
	{
		self::destroySession();
	}

	static function isLoggedIn()
	{
		return (self::getCurrentUser() !== false);
	}
}