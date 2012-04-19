<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('controllers/authentication.php');
require_once('models/assignment.php');
require_once('controllers/portfolio.php');
require_once('controllers/class.php');

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
		if ((!$user = AuthenticationController::get_current_user()) ||
			(!$assignment = Model::factory('Assignment')->create()))
		{
			return false;
		}
		// Create a Portfolio to contain submissions to this Assignment
		if (!$portfolio = PortfolioController::createPortfolio($title, $description, 1))
		{
			$assignment->destroy();		// Assume this succeeds
			return false;
		}
		
		if (!is_null($class_id) && $class = ClassController::getClass($class_id))
		{
			// Check for permissions on Class object here
			// $class->permissions

			$assignment->class_id = $class_id;
		}
		$assignment->owner_user_id = USER_ID;		// Check for User ID here
		$assignment->title = $title;
		$assignment->description = $description;
		$assignment->requirements = $requirements;

		if (!$assignment->save())
		{
			$assignment->destroy();		// Assume these succeed
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
		//currently just checks to see if you're logged in; DOES NOT CHECK EDIT PERMISSIONS
		if ((!$user = AuthenticationController::get_current_user()) ||
			(!$assignment = self::getAssignment($id)))
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
		if ((!$user = AuthenticationController::get_current_user()) ||
			(!$assignment = self::getAssignment($id)))
		{
			return false;
		}

		// Check permissions for editing the Assignment here
		// $assignment->permissions

		if (!is_null($owner_user_id))
		{
			// Check for ownership privileges here
			$assignment->owner_user_id = $owner_user_id;
		}
		if (!is_null($class_id) && ClassController::getClass($class_id))
		{
			// Check for ownership of Class
			$assignment->class_id = $class_id;
		}
		if (!is_null($title))		{ $assignment->title = $title; }
		if (!is_null($description))	{ $assignment->description = $description; }
		if (!is_null($requirements)){ $assignment->requirements = $requirements; }

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
		if ((!$user = AuthenticationController::get_current_user()) || 
			(!$assignment = self::getAssignment($id)))
		{
			return false;
		}

		// Check deletion privileges
		// $assignment->permissions

		return $assignment->delete();
	}

	/**
	 *	Un-deletes an Assignment.
	 *
	 *	Caller requires ownership privileges on deactivated Assignment.
	 *
	 *	@param	int		$id		Identifier of the Assignment to un-delete
	 *
	 *	@return	bool			True if successful, false otherwise
	 */
	public static function unDeleteAssignment($id)
	{
		if ((!$user = AuthenticationController::get_current_user()) || 
			(!$assignment = self::getAssignment($id)))
		{
			return false;
		}

		if ($assignment->creator_user_id === $user->user_id)
		{	// Should also check for Users with privileges aside from the sole User atatched to the Assignment
			//	i.e. co-owners
			return $assignment->unDelete();
		}
		else
		{
			return false;
		}
	}

	public static function addPermissionsToAssignment($id, $perm)
	{
		// Be sure to add permissions to the portfolio underneath the assignment!
	}


	/************************************************************************************
	 * AssignmentInstance object management												*
	 ***********************************************************************************/

	/**
	 *	Create a new AssignmentInstance from a master Assignment.
	 *
	 *	Calling User must have ownership privileges on the master Assignment.
	 *
	 *	@param	int			$id				Identifier of the master Assignmet we wish to instantiate
	 *	@param	string|null	$title			Overridden title of the instance (255 character max, optional)
	 *	@param	string|null	$description	Overridden description of the instance (2^16 character max, optional)
	 *	@param	string|null	$requirements	Overridden requirements of the instance (2^16 character max, optional)
	 *
	 *	@return	object|bool					The new AssignmentInstance if successful, false otherwise
	 */
	public static function instantiateAssignment($id, $title = NULL, $description = NULL, $requirements = NULL)
	{
	}

	/**
	 * 	Submit a unit of work (Project or Portfolio) to an AssignmentInstance.
	 *
	 * 	Calling User must have submission privileges for the Instance.
	 * 	Instance's due date must not have expired.
	 *
	 * 	@param	int		$assign_id			Identifier of the AssignmentInstance to add work to
	 * 	@param	int		$work_id			Identifier of the piece of work to add
	 * 	@param	bool	$work_is_portfolio	Whether or not the work is a Portfolio (true=portfolio)
	 *
	 * 	@return	bool						True if successful, false otherwise
	 */
	public static function submitWorkToAssignmentInstance($assign_id, $work_id, $work_is_portfolio)
	{
	}

	/**
	 *	Retrieve an AssignmentInstance for viewing.
	 *
	 *	Caller requires viewing privileges on the instance.
	 *
	 *	@param	int		$id		Identifier of the instance to retrieve
	 *
	 *	@return	object|bool		AssignmentInstance object if successful, false otherwise
	 */
	public static function viewAssignmentInstance($id)
	{
		if (!$instance = self::getAssignmentInstance($id))
		{
			return false;
		}

		// Check viewing permissions here
		// $instance->permissions
		
		return $instance;
	}

	/**
	 *	Retrieves an AssignmentInstance for internal use.
	 *
	 *	Does not check for permissions.
	 *
	 *	@param	int		$id		Identifier of the instance to retrieve
	 *
	 *	@return	object|bool		AssignmentInstance object if successful, false otherwise
	 */
	public static function getAssignmentInstance($id)
	{
		return Model::factory('AssignmentInstance')->find_one($id);
	}

	/**
	 *	Returns all instances of a specific Assignment.
	 *
	 *	@param	int		$id		Identifier of the Assignment to find AssignmentInstances of
	 *
	 *	@return	array|bool		Array of AssignmentInstance objects if successful, false otherwise
	 */
	private static function viewInstancesOfAssignment($id)
	{
		if (!$assignment = self::getAssignment($id))
		{
			return false;
		}

		$return = array();
		foreach ($assignment->instances as $instance)
		{
			// Check if User has permission to view AssignmentInstance
			// $instance->permissions
			$return[] = $instance;
		}

		return $return;
	}
}

?>
