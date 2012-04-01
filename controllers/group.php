<?php

/**
 * Group controller.
 * @package Controllers
 */
class GroupController
{
	/**
	 *	Creates a group and adds it to the database.
	 *		@param string title is the title of the group
	 *		@param string description is the description of the group
	 *		@param bool private is true if the group is not globally visible, false otherwise
	 *
	 *	@return the Group object if creation was successful, otherwise false
	 */
	static function createGroup($title, $description, $private)
	{
		if (!$newGroup = Model::factory('Group')->create())
		{
			return false;
		}

		$newGroup->title = $title;
		$newGroup->description = $description;
		$newGroup->private = $private;
		// $newGroup->owner_user_id = USER_ID
		$newGroup->owner_user_id = 1;	// should find auth'd user's ID

		if (!$newGroup->save())
		{
			$newGroup->delete();	// we assume this succeeds, else garbage collects in DB
			return false;
		}

		return $newGroup;
	}

	/**
	 *	Deletes a group with the specified ID.
	 *		@param int id is the ID of the group to delete
	 *
	 *	@return true if deletion was successful, otherwise false
	 */
	static function deleteGroup($id)
	{
		if (!$toDelete = Model::factory('Group')->find_one($id))
		{
			return false;
		}

		return $toDelete->delete();
	}

	/**
	 * Gets a Group object with the specified ID.
	 *		@param int id is the ID of the group to look for
	 *	
	 *	@return the Group object if one was found, otherwise false
	 */
	static function getGroup($id)
	{
		return Model::factory('Group')->find_one($id);
	}

	/**
	 * Updates a Group object with the specified ID.
	 *		@param string|null title is the new title of the group
	 *		@param string|null description is the description of the group
	 *		@param bool|null global specifies whether or not the group is global
	 *		@param int|null owner is the owner of the group (still not sure what this corresponds to)
	 *		@param int|null type is the new type of the group
	 *
	 *	@return true if the update was successful, otherwise false
	 */
	static function updateGroup($id, $title = NULL, $description = NULL, $global = NULL, $owner = NULL, $type = NULL)
	{
		if (!$groupToUpdate = self::getGroup($id))
		{
			return false;
		}

		if (isset($title)		{ $groupToUpdate->title = $title; }
		if (isset($description)	{ $groupToUpdate->description = $description; }
		if (isset($global)		{ $groupToUpdate->global = $global; }
		if (isset($owner)		{ $groupToUpdate->owner = $owner; }
		if (isset($type)		{ $groupToUpdate->type = $type; }

		return $groupToUpdate->save();
	}
}

?>
