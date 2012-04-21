<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/project.php');

/**
 * Controller handling Project objects
 *
 * @package Controllers
 */
class ProjectController
{
	/**
	 *	Create a new Project object with the specified creator_id, title, abstract, description, and privacy.
	 *		@param int creator_user_id is the ID of the user creating the project
	 *		@param string title is the title of the project
	 *		@param string abstract is the abstract of the project
	 *		@param string description is the description of the project
	 *
	 *	@return the created Project object if successful, false otherwise.
	 */
	public static function createProject($title, $description, $type)
	{
		// Check for creation privileges
		if ((!$user_id = AuthenticationController::get_current_user_id()) ||
			(!$project = Model::factory('Project')->create()))
		{
			return false;
		}

		$project->addPermissionForUser($user_id);
		$project->title = $title;
		$project->description = $description;
		$project->type = $type;

		if (!$project->save())
		{
			$project->delete();
			return false;
		}

		return $project;
	}

	/**
	 *	Edit a specific project, if the current user has permissions.
	 * 		@param int id is the ID of the project being edited
	 *		@param string abstract is the abstract of the project. The abstract will not be changed if an empty string is passed.
	 *		@param string is the description of the project. The description will not be changed if an empty string is passed
	 *
	 *	@return true if the project was successfully edited, false otherwise
	 */
	public static function editProject($id, $title = NULL, $description = NULL, $type = NULL)
	{
		$project = self::getProject($id);

		if (!$project)
		{
			return false;
		}

		// Check for editing permissions

		if (!is_null($title))		{ $project->title = $title; }
		if (!is_null($description))	{ $project->description = $description;	}
		if (!is_null($type))		{ $project->type = $type; }

		return $project->save();
	}

	/**
	 * Deletes a specific project that the user owns.
	 *		@param int id is the ID of the project to delete
	 *
	 *	@return true if the project was successfully deleted, otherwise false
	 */
	public static function deleteProject($id)
	{
		$project = self::getProject($id);

		if (!$project)
		{
			return false;
		}

		// Check for deletion permissions
		
		return $project->delete();
	}

	/**
	 * Returns a Project for viewing by the caller.
	 * Checks that caller has viewing permissions for the Project.
	 * 		@param int id is the ID of the project to view.
	 *
	 * @return The Project object if successful, false otherwise.
	 */
	public static function viewProject($id)
	{
		$project = self::getProject($id);
		
		if (!$project)
		{
			return false;
		}

		// Check for viewing permissions
		
		return $project;
	}


	/**
	 * Retrieve a specific Project object.
	 *		@param int id is the ID of the project to retrieve.
	 *
	 * @return the Project object requested if found, otherwise false
	 */
	private static function getProject($id)
	{
		return Model::factory('Project')->find_one($id);
	}
}

?>
