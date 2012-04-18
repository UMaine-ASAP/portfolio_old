<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/user.php');

/**
 *	@package Controllers
 */
class UserController
{
	/**
	 *	Create a new User object in the system.
	 *	
	 *	Creates a new User object with the specified parameters and adds it to the system.
	 *	Does not perform authentication to determine whether a User has privileges to create
	 *	a new User.
	 *	Nullable fields will be either set to their defaults or set to NULL in the system.
	 *	
	 *	@param	string		$username		Plain-text username of the User (255 character max)
	 *	@param	string		$pass			Hashed password of the User (255 character max)
	 *	@param	string		$first			Plain-text firstname of the User (255 character max)
	 *	@param	string|null	$middle			Plain-text middle name of the User  (255 character max, optional)
	 *	@param	string		$last			Plain-text last name of the User (255 character max)
	 *	@param	string|null	$email			Plain-text email address of the User (255 character max)
	 *	@param	bool|null	$email_priv		Whether or not the User's email is private 
	 *										(true=private, null will be set to private)
	 *	@param	string|null	$addn_conact	Plain-text additional contact information for the User  (255 character max, optional)
	 *	@param	string|null	$bio			Plain-text biographical entry for the User (2^16 character max, optional)
	 *	@param	string|null	$user_pic		Plain-text path to the User's picture (2^16 character max, optional)
	 *	@param	string|null	$major			Comma-separated list of unique identifiers of Departments the User is majoring in (255 character max, optional)
	 *	@param	string|null	$minor			Comma-separated list of unique identifiers of Departments the User is minoring in (255 character max, optional)
	 *	@param	int|null	$grad_year		Integer representing the year the User will/did graduate from UMaine (optional)
	 *	@param	int			$type_id		Identifier of the UserType of the User
	 *
	 *	@return	object|bool					The User object if successful, false otherwise
	 */
	public static function createUser($username, $pass, $first, $middle, $last, $email, $email_priv, $addn_contact, $bio, $user_pic, $major, $minor, $grad_year, $type_id)
	{
		if (!$user = Model::factory('User')->create())
		{
			return false;
		}

		$user->username = $username;
		$user->pass = $pass;
		$user->first = $first;
		$user->last = $last;
		$user->type_id = $type_id;
		$user->middle = $middle;
		$user->email = $email;
		$user->email_priv = $email_priv;
		$user->addn_contact = $addn_contact;
		$user->bio = $bio;
		$user->user_pic = $user_pic;
		$user->major = $major;
		$user->minor = $minor;
		$user->grad_year = $grad_year;

		if(!$user->save())
		{
			$user->delete();	// Remove erroneous User, assume this succeeds
			return false;
		}

		return $user;
	}

	/**
	 *	Retrieves a User object from the system.
	 *
	 *	Does not check privileges to retrieve the User.
	 *
	 *	@param	int		$userID		The identifier of the User to be retrieved.
	 *
	 *	@return object|bool			The User object if successful, false otherwise.
	 */
	public static function getUser($userID)
	{
		return Model::factory('User')->find_one($userID);
	}

	/**
	 *	Edit an existing User object in the system.
	 *	
	 *	Edits an existing User object in the system with the specified parameters.
	 *	Checks permissions on whether or not the current User has permissions to edit
	 *	the specified User (aka, *is* the specified User).
	 *	Any field(aside from $userID) may be specified as NULL to skip editing it.
	 *	
	 *	@param	int			$userID			Identifier of the User being edited
	 *	@param	string|null	$username		Plain-text username of the User (255 character max)
	 *	@param	string|null	$pass			Hashed password of the User (255 character max)
	 *	@param	string|null	$first			Plain-text firstname of the User (255 character max)
	 *	@param	string|null	$middle			Plain-text middle name of the User  (255 character max, optional)
	 *	@param	string|null	$last			Plain-text last name of the User (255 character max)
	 *	@param	string|null	$email			Plain-text email address of the User (255 character max)
	 *	@param	bool|null	$email_priv		Whether or not the User's email is private 
	 *										(true=private, null will be set to private)
	 *	@param	string|null	$addn_conact	Plain-text additional contact information for the User  (255 character max, optional)
	 *	@param	string|null	$bio			Plain-text biographical entry for the User (2^16 character max, optional)
	 *	@param	string|null	$user_pic		Plain-text path to the User's picture (2^16 character max, optional)
	 *	@param	string|null	$major			Comma-separated list of unique identifiers of Departments the User is majoring in (255 character max, optional)
	 *	@param	string|null	$minor			Comma-separated list of unique identifiers of Departments the User is minoring in (255 character max, optional)
	 *	@param	int|null	$grad_year		Integer representing the year the User will/did graduate from UMaine (optional)
	 *	@param	int|null	$type_id		Identifier of the UserType of the User
	 *
	 *	@return	bool						True if successful, false otherwise
	 */
	public static function editUser($userID, $username = NULL, $pass = NULL, $first = NULL, $middle = NULL, $last = NULL, $email = NULL, $email_priv = NULL, $addn_contact = NULL, $bio = NULL, $user_pic = NULL, $major = NULL, $minor = NULL, $grad_year = NULL, $type_id = NULL)
	{
		// Check current User's credentials here

		if (!$user = self::getUser($userID))
		{
			return false;
		}

		if (!is_null($username))	{ $user->username = $username; }
		if (!is_null($pass))		{ $user->pass = $pass; }
		if (!is_null($first))		{ $user->first = $first; }
		if (!is_null($middle))		{ $user->middle = $middle; }
		if (!is_null($last))		{ $user->last = $last; }
		if (!is_null($email))		{ $user->email = $email; }
		if (!is_null($email_priv))	{ $user->email_priv = $email_priv; }
		if (!is_null($addn_contact)){ $user->addn_contact = $addn_contact; }
		if (!is_null($bio))			{ $user->bio = $bio; }
		if (!is_null($user_pic))	{ $user->user_pic = $user_pic; }
		if (!is_null($major))		{ $user->major = $major; }
		if (!is_null($minor))		{ $user->minor = $minor; }
		if (!is_null($grad_year))	{ $user->grad_year = $grad_year; }
		if (!is_null($type_id))		{ $user->grad_year = $type_id; }

		return $user->save();
	}

	/**
	 *	"Deletes" a specified User from the system.
	 *
	 *	Checks whether the currently logged in User has permissions to delete the User.
	 *	NOTE: Doesn't truly delete the User from the system, only deactivates the User's account.
	 *	This is done to preserve links to this User by works in the system.
	 *
	 *	@param	int		$userID		Identifier of the User to be deactivated
	 *
	 *	@return	bool				True if successful, false otherwise.
	 */
	public static function deactivateUser($userID)
	{
		// Check any credentials here

		if (!$user = self::getUser($userID))
		{
			return false;
		}

		$user->deactivated = true;

		return $user->save();
	}

	/**
	 *	"Revives" a specified User in the system that had previously been deactivates.
	 *
	 *	Checks whether the currently logged in User has permissions to reactivate the User.
	 *
	 *	@param	int		$userID		Identifier of the User to be reactivated
	 *
	 *	@return	bool				True if successful, false otherwise.
	 */
	public static function reactivateUser($userID)
	{
		// Check any credentials here
		
		if (!$user = self::getUser($userID))
		{
			return false;
		}

		$user->deactivated = false;

		return $user->save();
	}
}
