<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/assignment.php');
require_once('controllers/portfolio.php');

/**
 * Assignment controller.
 *
 * @package Controllers
 */
class AssignmentController
{
	/************************************************************************************
	 * Assignment object management														*
	 ***********************************************************************************/

	/**
	 *	Creates a new Assignment object in the system.
	 *
	 *	The creating User must have the required privileges on the Class to which the Assignment belongs
	 *	(if a Class is specified).
	 *
	 *	@param	int|null	$class_id		Identifier of the Class the Assignment belongs to (optional)
	 *	@param	string		$title			Plain-text title of the Assignment (255 character limit)
	 *	@param	string|null	$description	Plain-text description of the Assignment (2^16 character limit, optional)
	 *	@param	string|null	$requirements	Plain-text desctiption of the requirements of the Assignment (2^16 character limit, optional)
	 *
	 *	@return object|bool					The created Assignment object if successful, false otherwise
 	 */
	public static function createAssignment($class_id, $title, $description, $requirements)
	{
		if (!$assignment = Model::factory('Assignment')->create())
		{
			return false;
		}
		// Create a Portfolio to contain submissions to this Assignment
		if (!$portfolio = PortfolioController::createPortfolio($title, $description, 1))
		{
			$assignment->delete();		// Assume this succeeds
			return false;
		}
		
		if (!is_null($class_id) && $class = ClassController::getClass($class_id))
		{
			// Check for permissions on Class object here
			// $class->permissions

			$assignment->class_id = $class_id;
		}
		$assignment->portfolio_id = $portfolio->id();
		$assignment->creator_user_id = USER_ID;		// Check for User ID here
		$assignment->title = $title;
		$assignment->description = $description;
		$assignment->requirements = $requirements;

		if (!$assignment->save())
		{
			$assignment->delete();		// Assume these succeed
			$portfolio->delete();		// 
			return false;
		}

		return $assignment;
	}

	/**
	 *	Gets a specific Assignment object for private use.
	 *
	 *	Does not check permissions.
	 *
	 *	@param	int		$id		Identifier of the Assignment object to get
	 *
	 *	@return	object|bool		The Assignment object if found, false otherwise
	 */
	private static function getAssignment($id)
	{
		return Model::factory('Assignment')->find_one($id);
	}

	/**
	 *	Gets a specified Assignment object for public viewing.
	 *
	 *	Checks that the requesting User has viewing privileges on the Assignment.
	 *
	 *	@param	int		$id		Identifier of the Assignment object to get
	 *
	 *	@return	object|bool		The Assignment object if found, false otherwise
	 */
	public static function viewAssignment($id)
	{
		if (!$assignment = self::getAssignment($id))
		{
			return false;
		}

		// Check privileges here
		// $assignment->privileges

		return $assignment;
	}

	/**
	 *	Edit a specific Assignment object.
	 *	
	 *	Checks that the current user has (at least) editing privileges on the Assignment.
	 *	Parameters with a NULL value will be ignored and left unchanged.
	 *
	 *	@param	int				$id				Identifier of the assignment to edit
	 *	@param	int|null		$owner_user_id	Identifier of the User to change the Assignment's owner to
	 *											(requires ownership privileges on the Assignment)
	 *	@param	int|null		$class_id		Identifier of the class owning the Assignment
	 *	@param	string|null		$title			Title of the Assignment (plain-text, 255 character max)
	 *	@param	string|null		$description	Description of the Assignment (plain-text, 2^16 character max)
	 *	@param	string|null		$requirements	Requirements of the Assignment to be fulfilled by submissions (plain-text, 2^16 character max)
	 *
	 *	@return	bool							True if successful, false otherwise
	 */
	public static function editAssignment($id, $owner_user_id = NULL, $class_id = NULL, $title = NULL, $description = NULL, $requirements = NULL)
	{
		if (!$assignment = Model::factory('Assignment')->find_one($id))
		{
			return false;
		}

		// Check permissions for editing the Assignment here
		// $assignment->permissions

		if (!is_null($section_id))	{ $assignment->section_id = $section_id; }
		if (!is_null($group_id))	{ $assignment->group_id = $group_id; }
		if (!is_null($owner_user_id))
		{
			// Check for ownership privileges here
			$assignment->owner_user_id = $owner_user_id;
		}
		if (!is_null($collect_id))	{ $assignment->collect_id = $collect_id; }
		if (!is_null($title))		{ $assignment->title = $title; }
		if (!is_null($description))	{ $assignment->description = $description; }

		return $assignment->save();
	}

	/**
	 *	Deletes a specific Assignment.
	 *
	 *	Caller requires deletion privileges.
	 *
	 *	@param	int		$id		Identifier of the Assignment to delete
	 *
	 *	@return	bool			True if successful, false otherwise
	 */
	public static function deleteAssignment($id)
	{
		if (!$assignment = Model::factory('Assignment')->find_one($id))
		{
			return false;
		}

		// Check deletion privileges
		// $assignment->permissions

		return $assignment->delete();
	}

	public static function addPermissionsToAssignment($id, $perm)
	{
		// Be sure to add permissions to the portfolio underneath the assignment!
	}
}

?>
