<?php
//require_once('libraries/Idiorm/idiorm.php');
//require_once('libraries/Paris/paris.php');

class GroupController
{
	/**
	 * Creates a Group with the specified attributes.
	 *		@param string name is the name of the group
	 *		@param string description is the description of the group
	 *		@param bool global specifies whether or not the group is global
	 *		@param int owner is the owner of the group (not sure what this corresponds to)
	 *		@param int type is the type of the group
	 *
	 *	Returns: the Group object if creation was successful, otherwise false
	 */
	static function createGroup($name, $description, $global, $owner, $type)
	{
		$newGroup = Model::factory('Group')->create();

		$newGroup->name = $name;
		$newGroup->description = $description;
		$newGroup->global = $global;
		$newGroup->owner = $owner;
		$newGroup->type = $type;

		if (!$newGroup->save())
		{
			return false;
		}

		return $newGroup;
	}

	/**
	 *	Deletes a group with the specified ID.
	 *		@param int id is the ID of the group to delete
	 *
	 *	Returns: true if deletion was successful, otherwise false
	 */
	static function deleteGroup($id)
	{
		$toDelete = Model::factory('Group')->find_one($id);

		if(!toDelete)
		{
			return false;
		}

		$toDelete->delete();
		return true;
	}

	/**
	 * Gets a Group object with the specified ID.
	 *		@param int id is the ID of the group to look for
	 *	
	 *	Returns: the Group object if one was found, otherwise false
	 */
	static function getGroup($id)
	{
		return Model::factory('Group')->find_one($id);
	}

	/**
	 * Updates a Group object with the specified ID.
	 *		@param string name is the new name of the group
	 *		@param string description is the description of the group
	 *		@param bool global specifies whether or not the group is global
	 *		@param int owner is the owner of the group (still not sure what this corresponds to)
	 *		@param int type is the new type of the group
	 *
	 *	Returns: true if the update was successful, otherwise false
	 */
	static function updateGroup($id, $name, $description, $global, $owner, $type)
	{
		$groupToUpdate = Model::factory('Group')->find_one($id);

		if(!$groupToUpdate)
		{
			return false;
		}

		$groupToUpdate->name = $name;
		$groupToUpdate->description = $description;
		$groupToUpdate->global = $global;
		$groupToUpdate->owner = $owner;
		$groupToUpdate->type = $type;

		$groupToUpdate->save();
		return true;
	}
}

?>
