<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/project.php');
require_once('controllers/authentication.php');

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
	 *		@param string description is the description of the project
	 *		@param int type is the type of the Project
	 *
	 *	@return the created Project object if successful, false otherwise.
	 */
	public static function createProject($title, $description, $type)
	{
		// Check for creation privileges (for now, only that a User is logged in)
		if ((!$user_id = AuthenticationController::get_current_user_id()) ||
			(!$project = Model::factory('Project')->create()))
		{
			return false;
		}

		$project->title = $title;
		$project->description = $description;
		$project->type = $type;
		if (!$project->save())
		{
			return false;
		}

		// Add permissions for the creator (done after save so that the Project will have an ID)
		$project->addPermissionForUser($user_id, OWNER);
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
		if ((!$user_id = AuthenticationController::get_current_user_id()) ||
			(!$project = self::getProject($id)) ||
			(!$project->havePermissionOrHigher(EDIT)))	// Check for EDIT privileges
		{
			return false;
		}

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
		if ((!$user_id = AuthenticationController::get_current_user_id()) ||
			(!$project = self::getProject($id)) ||
			(!$project->havePermissionOrHigher(OWNER)))	// User musr have OWNER permissions on project
		{
			return false;
		}
		
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
		if ((!$user_id = AuthenticationController::get_current_user_id()) ||
			(!$project = self::getProject($id)) ||
			(!$project->havePermissionOrHigher(READ)))	// User musr have READ permissions on project
		{
			return false;
		}
		
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
	
	/**
	 *	Add a Media object to a Project object.
	 *
	 *	Calling user must have WRITE privileges to the parent Project, and OWNER
	 *	privileges to the child Media.
	 *	
	 *	@param	int		$proj_id		Identifier of the Project object to add Media to
	 *	@param	int		$media_id		Identifier of the Media object to add to the Project
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function addMediaToProject($proj_id, $media_id)
	{
		if ((!$user_id = AuthenticationController::get_current_user_id()) ||
			(!$project = self::getProject($proj_id)) ||
			(!$project->havePermissionOrHigher(WRITE)) ||	// User must have WRITE permission on parent
			(!$media = MediaController::viewMedia($media_id)) ||	// Requires 'viewing' (or higher, assumed) permissions on the Media
			(!$media->havePermissionOrHigher(OWNER)))		// User must have OWNER permission on child
		{
			return false;
		}

		return $project->addMedia($media_id);
	}

	/**
	 *	Remove a 'child' Media object from a Project object.
	 *
	 *	Calling user must have WRITE privileges of the Project object.
	 *	
	 *	@param	int		$proj_id		Identifier of the Project the Media is to be added to
	 *	@param	int		$media_id		Identifier of the Media to be added to the Project
	 *
	 *	@return	bool					True if successful, false otherwise.
	 */
	public static function removeChildFromPortfolio($parent_id, $child_id, $is_portfolio)
	{
		if ((!$user_id = AuthenticationController::get_current_user_id()) ||
			(!$project = self::getProject($proj_id)) ||
			(!$media = MediaController::getMedia($media_id)) ||
			(!$media->havePermissionOrHigher(WRITE)) ||	// User must have WRITE permission on parent
			(!in_array($media_id, $project->media)))
		{
			return false;
		}

		return $project->removeMedia($media_id);
	}
}

?>
