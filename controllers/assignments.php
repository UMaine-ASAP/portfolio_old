<?php
//require_once('libraries/Idiorm/idiorm.php');
//require_once('libraries/Paris/paris.php');


class AssignmentController
{
	/**
	 * Creates a new Assignment object, then adds it to the DB.
	 *		@param int section_id is the ID of the section the assignment belongs to
	 *		@param int group_id is the ID of the group the assignment belongs to
	 *		@param int owner_id is the ID of the group that owns the assignment (?)
	 *		@param int collect_id is the ID of the assignment's collection_project_map
	 *		@param string name is the name of the assignment
	 *		@param string description is the description of the assignment
	 *
	 *	@return the Assignment object if creation was successful, otherwise false
 	 */

	static function createAssignment($section_id, $group_id, $owner_id, $collect_id, $name, $description)
	{
		$assignment = Model::factory('Assignment')->create();

		$assignment->section_id = $section_id;
		$assignment->group_id = $group_id;
		$assignment->owner_id = $owner_id;
		$assignment->collect_id = $collect_id;
		$assignment->name = $name;
		$assignment->description = $description;

		if (!$assignment->save())
		{
			return false;
		}

		return $assignment;
	}

	/**
	 *	Gets a specific Assignment object.
	 *		@param int $id is the ID of the project to get
	 *
	 *	@return the Assignment object if found, false otherwise
	 */
	static function getAssignment($id)
	{
		return Model::factory('Assignment')->find_one($id);
	}

	/**
	 * Edit a specific Assigment object that the current user has (at least) editing privileges for
	 *		@param int $id is the ID of the assignment to edit
	 *		@param int section_id is the new section ID of the assignment
	 *		@param int $group_id is the new group ID of the assignment
	 *		@param int $owner_id is the new owner ID of the assignment
	 *		@param int collect_id is the new collection_project_map ID of the assignment
	 *		@param string name is the new name of the assignment
	 *		@param string description is the new description of the assignment
	 *
	 *	@return true if the edit was successful, false otherwise
	 */
	static function editAssignment($id, $section_id, $group_id, $owner_id, $collect_id, $name, $description)
	{
		$assignment = Model::factory('Assignment')->find_one($id);

		if(!$assignment)
		{
			return false;
		}

		$assignment->section_id = $section_id;
		$assignment->group_id = $group_id;
		$assignment->owner_id = $owner_id;
		$assignment->collect_id = $collect_id;
		$assignment->name = $name;
		$assignment->description = $description;

		if (!$assignment->save())
		{
			return false;
		}

		return true;
	}

	/**
	 * Deletes an Assignment with the specified ID.
	 *		@param int id is the ID of the assignment to delete
	 *
	 *	@return true if deletion succeeded, otherwise false
	 */
	static function deleteAssignment($id)
	{
		$assignment = Model::factory('Assignment')->find_one($id);

		if(!$assignment)
		{
			return false;
		}

		$assignment->delete();
	}

	/**
	 * Returns the group that owns a specified assignment.
	 *		@param int id is the ID of the assigment whose owning group will be returned
	 *
	 *	@return the Group object corresponding to the assignment's owning group if found, otherwise false
	 */
	static function getOwnerGroup($id)
	{
		$assignment = Model::factory('Assignment')->find_one($id);

		if(!$assignment)
		{
			return false;
		}

		return = Model::factory('Group')
					-> where('group_id', $assignment->owner_id)
					-> find_one();
	}
}

?>