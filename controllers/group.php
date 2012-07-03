<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/group.php');

/**
 * Group controller.
 *
 * @package Controllers
 */
class GroupController
{
	/**
	 *	Creates a group and adds it to the database.
	 *	User must be logged in with correct privileges.
	 *
	 *		@param string name is the name of the group	(2^16 character max,
	 *			to accomodate names of 255 character Portfolios, etc. appended to)
	 *		@param string description is the description of the group (2^16 character max)
	 *		@param bool private is true if the group is not globally visible, false otherwise
	 *
	 *	@return the Group object if creation was successful, otherwise false
	 */
	public static function createGroup($name, $description, $private)
	{
		//We don't currently check for creation privileges, just to make sure that the user is logged in
		if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
			(!$newGroup = Model::factory('Group')->create()))
		{
			return false;
		}

		if (!is_null($name))	{ $newGroup->name = $name; }
		if (!is_null($private))	{ $newGroup->private = $private; }
		$newGroup->owner_user_id = $user_id;
		$newGroup->description = $description;

		if (!$newGroup->save())
		{
			$newGroup->delete();	// we assume this succeeds, else garbage collects in DB
			return false;
		}

		return $newGroup;
	}

	/**
	 *	Deletes a group with the specified ID.
	 *
	 *	Calling user must have ownership permissions on the Group.
	 *
	 *	@param int id is the ID of the group to delete
	 *
	 *	@return true if deletion was successful, otherwise false
	 */
	public static function deleteGroup($id)
	{
		if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
			(!$toDelete = GroupController::getGroup($id)))
		{
			return false;
		}

		if ($user->user_id === $toDelete->owner_user_id)
		{
			return $toDelete->delete();
		}

		return false;
	}

	/**
	 * 	Gets a Group object for the purpose of viewing
	 *
	 * 	@param	int		$id		The identifier of the requested Group object.
	 *
	 * 	@return	object|bool		The Group object if successful, false otherwise.
	 */
	public static function viewGroup($id)
	{
		if (!$group = GroupController::getGroup($id))
		{
			return false;
		}

		//TODO: Check for viewing permissions
		// $group->permissions

		return $group;
	}

	/**
	 * Gets a Group object with the specified ID.
	 *		@param int id is the ID of the group to look for
	 *
	 *	@return the Group object if one was found, otherwise false
	 */
	private static function getGroup($id)
	{
		return Model::factory('Group')->find_one($id);
	}

	/**
	 *	Edits a Group object with the specified ID.
	 *	
	 *	Calling User must have OWNER permissions on the Group
	 *
	 *	@param	string|null	$name			New name of the group
	 *	@param	string|null $description	Description of the group
	 *	@param	bool|null 	$private		Whether or not the group is publicly visible (true=private)
	 *	@param	int|null 	$owner_user_id	Identifier of the User who owns the group
	 *		
	 *	@return								True if the update was successful, otherwise false
	 */
	public static function editGroup($id, $name = NULL, $description = NULL, $private = NULL, $owner_user_id = NULL)
	{
		if ((!$groupToUpdate = self::getGroup($id)) ||
			(!$user_id = AuthenticationController::getCurrentUserID()))
		{
			return false;
		}

		//TODO: Check for editing permissions
		// $groupToUpdate->permissions

		if (!is_null($name))			{ $groupToUpdate->name = $name; }
		if (!is_null($description))		{ $groupToUpdate->description = $description; }
		if (!is_null($private))			{ $groupToUpdate->private = $private; }
		if (!is_null($owner_user_id))	{ $groupToUpdate->owner_user_id = $owner_user_id; }

		return $groupToUpdate->save();
	}

	/**
	 *	Add a User to a specific Group object.
	 *
	 *	Calling User must have OWNER permissions on the Group the User is being added to.
	 *
	 *	@param	int		$group_id		Identifier of the Group to add the User to
	 *	@param	int		$user_id		Identifier of the User to add to the Group
	 *
	 *	@return	bool					True if successful, false otherwise
	 */
	public static function addUserToGroup($group_id, $user_id)
	{
		if ((!$group = self::getGroup($group_id)) ||
			(!$user = UserController::getUser($user_id)) ||
			($group->owner_user_id != AuthenticationController::getCurrentUserID()))
		{
			return false;
		}

		return $group->addUser($user_id);
	}

	/**
	 *	Remove a User from a specific Group object.
	 *
	 *	Calling User must have OWNER permissions on the Group the User is being removed from.
	 *
	 *	@param	int		$group_id		Identifier of the Group to remove the User from
	 *	@param	int		$user_id		Identifier of the User to remove from the Group
	 *
	 *	@return	bool					True if successful, false otherwise
	 */
	public static function removeUserFromGroup($group_id, $user_id)
	{
		if ((!$group = self::getGroup($group_id)) ||
			($group->owner_user_id != AuthenticationController::getCurrentUserID()))
		{
			return false;
		}

		return $group->removeUser($user_id);
	}

}

?>
