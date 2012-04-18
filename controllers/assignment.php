<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('controllers/authentication.php');
require_once('models/assignment.php');
require_once('controllers/portfolio.php');

/**
 * Assignment controller.
 *
 * @package Controllers
 */
class AssignmentController
{
	/**
	 *	Creates a new Assignment object, then adds it to the DB.
	 *		@param int section_id is the ID of the section the assignment belongs to
	 *		@param int group_id is the ID of the group the assignment belongs to
	 *		@param int owner_id is the ID of the group that owns the assignment (?)
	 *		@param int collect_id is the ID of the assignment's collection_project_map
	 *		@param string title is the title of the assignment
	 *		@param string description is the description of the assignment
	 *
	 *	@return the Assignment object if creation was successful, otherwise false
 	 */
	public static function createAssignment($section_id, $group_id, $collect_id, $title, $description, $requirements)
	{
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}
		if (!$assignment = Model::factory('Assignment')->create())
		{
			return false;
		}
		// Create a Portfolio to contain submissions to this Assignment
		if (!$portfolio = PortfolioController::createPortfolio($title, $description, 1))
		{
			return false;
		}

		$assignment->section_id = $section_id;
		$assignment->portfolio_id = $portfolio->id();
		$assignment->creator_user_id = $user->user_id;
		$assignment->title = $title;
		$assignment->description = $description;
		$assignment->requirements = $requirements;

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
	public static function getAssignment($id)
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
	 *		@param string title is the new title of the assignment
	 *		@param string description is the new description of the assignment
	 *
	 *	@return true if the edit was successful, false otherwise
	 */
	public static function editAssignment($id, $section_id, $group_id, $owner_id, $collect_id, $title, $description)
	{
		//currently just checks to see if you're logged in; DOES NOT CHECK EDIT PERMISSIONS
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}

		$assignment = Model::factory('Assignment')->find_one($id);

		if(!$assignment)
		{
			return false;
		}

		$assignment->section_id = $section_id;
		$assignment->group_id = $group_id;
		$assignment->owner_id = $owner_id;
		$assignment->collect_id = $collect_id;
		$assignment->title = $title;
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
	public static function deleteAssignment($id)
	{
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}

		$assignment = Model::factory('Assignment')->find_one($id);

		if(!$assignment)
		{
			return false;
		}

		if ($assignment->creator_user_id === $user->user_id)
		{
			return $assignment->delete();
		}

		return false;
	}

	public static function addPermissionsToAssignment($id, $perm)
	{
		// Be sure to add permissions to the portfolio underneath the assignment!
	}
}

?>
