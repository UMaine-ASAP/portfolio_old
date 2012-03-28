<?php
require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');


class ProjectController
{
	/**
	 *	Create a new Project object with the specified creator_id, name, abstract, description, and privacy.
	 *		@param int creator_id is the ID of the user creating the project
	 *		@param string name is the name of the project
	 *		@param string abstract is the abstract of the project
	 *		@param string description is the description of the project
	 *		@param bool privacy is the privacy of the project
	 *
	 *	@return the created Project object if successful, false otherwise.
	 */
	static function createProject($creator_id, $name, $abstract, $description, $privacy)
	{
		$project = Model::factory('Project')->create();
		$project->creator_id = $creator_id;
		$project->name = $name;
		$project->abstract = $abstract;
		$project->description = $description;
		$project->private = $privacy;

		if (!$project->save())
		{
			return false;
		}

		return $project;
	}

	/**
	 *	Edit a specific project, if the current user has permissions.
	 * 		@param int id is the ID of the project being edited
	 *		@param string abstract is the abstract of the project. The abstract will not be changed if an empty string is passed.
	 *		@param string is the description of the project. The description will not be changed if an empty string is passed
	 *		@param bool privacy is the project's privacy (TRUE for private, FALSE for public)
	 *
	 *	@return true if the project was successfully edited, false otherwise
	 */
	static function editProject($id, $abstract, $description, $privacy)
	{
		$project = self::getProject($id);

		//if there was no project found with that ID
		if (!$project)
		{
			return false;
		}

		if (!empty($abstract))
		{
			$project->abstract = $abstract;	
		}
		if (!empty($description))
		{
			$project->description = $description;
		}
		$project->privacy = $privacy;

		$project->save();

		return true;
	}

	/**
	 * Deletes a specific project that the user owns.
	 *		@param int id is the ID of the project to delete
	 *
	 *	@return true if the project was successfully deleted, otherwise false
	 */
	static function deleteProject($id)
	{
		$project = Model::factory('Project')->find_one($id);

		//if we found a project with that ID
		if (!$project)
		{
			return false;
		}

		$project->delete();
		return true;
	}

	/**
	 * Retrieve a specific Project object.
	 *		@param int id is the ID of the project to retrieve.
	 *
	 * @return the Project object requested if found, otherwise false
	 */
	static function getProject($id)
	{
		return Model::factory('Project')->find_one($id);
	}
}

?>