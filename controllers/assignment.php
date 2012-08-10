<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/assignment.php');
require_once('controllers/authentication.php');
require_once('controllers/portfolio.php');
require_once('controllers/project.php');
require_once('controllers/class.php');

/**
 * Assignment controller.
 *
 * @package Controllers
 */
class AssignmentController
{
    /*     * **********************************************************************************
     * Assignment object management														*
     * ********************************************************************************* */

    /**
     * 	Creates a new Assignment object in the system.
     *
     * 	The creating User must have the required privileges on the Class to which the Assignment belongs
     * 	(if a Class is specified).
     *
     * 	@param	int|null	$class_id		Identifier of the Class the Assignment belongs to (optional)
     * 	@param	string		$title			Plain-text title of the Assignment (255 character limit)
     * 	@param	string|null	$description	Plain-text description of the Assignment (2^16 character limit, optional)
     * 	@param	string|null	$requirements	Plain-text desctiption of the requirements of the Assignment (2^16 character limit, optional)
     *
     * 	@return object|bool					The created Assignment object if successful, false otherwise
     */
    public static function createAssignment($class_id, $title, $description, $requirements)
    {
        if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
                (!$assignment = Model::factory('Assignment')->create()))
        {
            return false;
        }

        if (!is_null($class_id) && $class = ClassController::getClass($class_id))
        {
            // Check for permissions on Class object here
            // $class->permissions

            $assignment->class_id = $class_id;
        }
        $assignment->title = $title;
        $assignment->description = $description;
        $assignment->requirements = $requirements;
        // Add current User as OWNER
        $assignment->addOwner($user_id->id());

        if (!$assignment->save())
        {
            $assignment->destroy();  // Assume these succeed
            return false;
        }

        return $assignment;
    }

    /**
     * 	Gets a specific Assignment object for private use.
     *
     * 	Does not check permissions.
     *
     * 	@param	int		$id		Identifier of the Assignment object to get
     *
     * 	@return	object|bool		The Assignment object if found, false otherwise
     */
    private static function getAssignment($id)
    {
        return Model::factory('Assignment')->find_one($id);
    }

    /**
     * Gets a specified Assignment object, first checking if the logged-in user has the proper permissions to do so.
     * 		@param int $id is the ID of the assignment
     *
     * 	@return the Assignment object if found, false if the user could not see it or it did not exist
     */
    public static function viewAssignment($id)
    {
        if ((!$assignment = self::getAssignment($id)) ||
                (!$user_id = AuthenticationController::getCurrentUserID()))
        {
            return false;
        }

        foreach ($assignment->permissions as $perm) //determine if we have the permission level required to view the assignment
        {
            if ($perm <= READ)
            {
                return self::getAssignment($id); //we have permission, so return the assignment
            }
        }

        return false;
    }

    /**
     * 	Edit a specific Assignment object.
     * 	
     * 	Checks that the current user has (at least) editing privileges on the Assignment.
     * 	Parameters with a NULL value will be ignored and left unchanged.
     *
     * 	@param	int				$id				Identifier of the assignment to edit
     * 	@param	int|null		$owner_user_id	Identifier of the User to change the Assignment's owner to
     * 											(requires ownership privileges on the Assignment)
     * 	@param	int|null		$class_id		Identifier of the class owning the Assignment
     * 	@param	string|null		$title			Title of the Assignment (plain-text, 255 character max)
     * 	@param	string|null		$description	Description of the Assignment (plain-text, 2^16 character max)
     * 	@param	string|null		$requirements	Requirements of the Assignment to be fulfilled by submissions (plain-text, 2^16 character max)
     *
     * 	@return	bool							True if successful, false otherwise
     */
    public static function editAssignment($id, $owner_user_id = NULL, $class_id = NULL, $title = NULL, $description = NULL, $requirements = NULL)
    {
        if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
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
        if (!is_null($title))
        {
            $assignment->title = $title;
        }
        if (!is_null($description))
        {
            $assignment->description = $description;
        }
        if (!is_null($requirements))
        {
            $assignment->requirements = $requirements;
        }

        return $assignment->save();
    }

    /**
     * 	Deletes a specific Assignment.
     *
     * 	Caller requires deletion privileges.
     *
     * 	@param	int		$id		Identifier of the Assignment to delete
     *
     * 	@return	bool			True if successful, false otherwise
     */
    public static function deleteAssignment($id)
    {
        if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
                (!$assignment = self::getAssignment($id)))
        {
            return false;
        }

        // Check deletion privileges
        // $assignment->permissions

        return $assignment->delete();
    }

    /**
     * 	Un-deletes an Assignment.
     *
     * 	Caller requires ownership privileges on deactivated Assignment.
     *
     * 	@param	int		$id		Identifier of the Assignment to un-delete
     *
     * 	@return	bool			True if successful, false otherwise
     */
    public static function unDeleteAssignment($id)
    {
        if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
                (!$assignment = self::getAssignment($id)))
        {
            return false;
        }

        if ($assignment->creator_user_id === $user->user_id)
        { // Should also check for Users with privileges aside from the sole User atatched to the Assignment
            //	i.e. co-owners
            return $assignment->unDelete();
        } else
        {
            return false;
        }
    }

    /**
     * 	Adds an additional User with OWNER level permissions to a specific Assignment.
     *
     * 	Calling User must also be an OWNER.
     *
     * 	@param	int		$assign_id		Identifier of the Assignment to add a new OWNER to
     * 	@param	int		$user_id		Identifier of the User to give OWNER permissions to
     *
     * 	@return	bool					True if successful, false otherwise
     */
    public static function addCoownerToAssignment($assign_id, $user_id)
    {
        if ((!$userID = AuthenticationController::getCurrentUserID() || !($assignment = self::getAssignment($assign_id))))
        {
            return false;
        }
        
        if (!$user = UserController::getUser($user_id))
        {
            return false;
        }
        
        return $assignment->addPermissionForUser($user_id, OWNER);    
    }

    /**
     * 	Remove a User with OWNER level permissions from a specific Assignment.
     *
     * 	Calling User must also be an OWNER.
     *
     * 	@param	int		$assign_id		Identifier of the Assignment to remove an OWNER from
     * 	@param	int		$user_id		Identifier of the User to remove OWNER permissiosn from
     *
     * 	@return	bool					True if successful, false otherwise
     */
    public static function removeCoownerFromAssignment($assign_id, $user_id)
    {
        if ((!$userID = AuthenticationController::getCurrentUserID() | !($assignment = self::getAssignment($assign_id))))
        {
            return false;
        }
        
        if (!$user = UserController::getUser($user_id) && !$user->havePermissionOrHigher(OWNER))
        {
            return false;
        }
        
        return $assignment->removePermissionForUser($user_id, OWNER);
    }

    /*     * **********************************************************************************
     * AssignmentInstance object management												*
     * ********************************************************************************* */

    /**
     * 	Create a new AssignmentInstance from a master Assignment.
     *
     * 	Calling User must have ownership privileges on the master Assignment.
     *
     * 	@param	int			$assign_id		Identifier of the master Assignment we wish to instantiate
     * 	@param	int			$section_id		Identifier of the Section we wish to add the Assignment to
     * 	@param	string|null	$title			Overridden title of the instance (255 character max, optional)
     * 	@param	string|null	$description	Overridden description of the instance (2^16 character max, optional)
     * 	@param	string|null	$requirements	Overridden requirements of the instance (2^16 character max, optional)
     * 	@param	string|null	$due_date		Date string formatted as YY-MM-DD hh-mm-ss representing the closing date of the instance
     *
     * 	@return	object|bool					The new AssignmentInstance if successful, false otherwise
     */
    public static function instantiateAssignment($assign_id, $section_id, $title = NULL, $description = NULL, $requirements = NULL, $due_date = NULL)
    {
        if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
                (!$assignment = self::getAssignment($assign_id)) ||
                (!$section = SectionController::getSection($section_id)) ||
                (!$instance = Model::factory('AssignmentInstance')->create()))
        {
            return false;
        }

        // Check that current User has ownership privileges on the master Assignment & Section
        // $assignment->permissions && $assignment->owner_user_id && $section->permissions && section->owner_user_id

        $instance->assign_id = $assign_id;
        $instance->section_id = $section_id;
        $instance->owner_user_id = USER_ID; // Retrieve User ID
        $instance->title = (is_null($title) ? $assignment->title : $title);
        $instance->description = (is_null($description) ? $assignment->description : $description);
        $instance->requirements = (is_null($requirements) ? $assignment->requirements : $requirements);
        if (!is_null($due_date))
        {
            $instance->due_date = $due_date;
        }
        // Create new Portfolio to hold the contents of this Instance
        // NOTE: Portfolio is created as private, permissions must be added to an Assignment
        if (!$portfolio = PortfolioController::createPortfolio($instance->title . " Assignment Submissions", $instance->description, true))
        {
            $instance->delete();
            return false;
        }
        $instance->portfolio_id = $portfolio->id();

        if (!$instance->save())
        {
            $portfolio->delete();
            $instance->delete();
            return false;
        }

        return $instance;
    }

    /**
     * 	Retrieve an AssignmentInstance for viewing.
     *
     * 	Caller requires viewing privileges on the instance.
     *
     * 	@param	int		$id		Identifier of the instance to retrieve
     *
     * 	@return	object|bool		AssignmentInstance object if successful, false otherwise
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
     * 	Retrieves an AssignmentInstance for internal use.
     *
     * 	Does not check for permissions.
     *
     * 	@param	int		$id		Identifier of the instance to retrieve
     *
     * 	@return	object|bool		AssignmentInstance object if successful, false otherwise
     */
    private static function getAssignmentInstance($id)
    {
        return Model::factory('AssignmentInstance')->find_one($id);
    }

    /**
     * 	Edit a specific AssignmentInstance.
     *
     * 	Calling User must have EDIT privileges or higher on the instance.
     *
     * 	@param	int|null	$owner_user_id	Identifier of the owner User object of the instance
     * 	@param	string|null	$title			Overridden title of the instance (255 character max, optional)
     * 	@param	string|null	$description	Overridden description of the instance (2^16 character max, optional)
     * 	@param	string|null	$requirements	Overridden requirements of the instance (2^16 character max, optional)
     * 	@param	string|null	$due_date		Date string formatted as YY-MM-DD hh-mm-ss representing the closing date of the instance
     *
     * 	@return	bool						True if successful, false otherwise
     */
    private static function editAssignmentInstance($id, $owner_user_id = NULL, $title = NULL, $description = NULL, $requirements = NULL, $due_date = NULL)
    {
        
    }

    /**
     * 	Delete specific AssignmentInstance
     *
     * 	Destroys the Portfolio containing work.
     *
     * 	@param	int		$id		Identifier of the AssignmentInstance to remove from the system
     *
     * 	@return	bool			True if successful, false otherwise
     */
    public static function deleteAssignmentInstance($id)
    {
        
    }

    /**
     * 	Returns all instances of a specific Assignment.
     *
     * 	@param	int		$id		Identifier of the Assignment to find AssignmentInstances of
     *
     * 	@return	array|bool		Array of AssignmentInstance objects if successful, false otherwise
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

    /**
     * 	Submit a unit of work (Project or Portfolio) to an AssignmentInstance.
     *
     * 	Calling User must have SUBMIT privileges for the Instance, and OWNER priviledges on the piece of work.
     * 	Instance's due date must not have expired.
     *
     * 	@param	int		$instance_id		Identifier of the AssignmentInstance to add work to
     * 	@param	int		$work_id			Identifier of the piece of work to add
     * 	@param	bool	$work_is_portfolio	Whether or not the work is a Portfolio (true=portfolio)
     *
     * 	@return	bool						True if successful, false otherwise
     */
    public static function submitWorkToAssignmentInstance($instance_id, $work_id, $work_is_portfolio)
    {
        if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
                (!$instance = self::getAssignmentInstance($instance_id)) ||
                (!($work_is_portfolio) && ($work = PortfolioController::viewPortfolio($work_id))) ||
                (!(!$work_is_portfolio) && ($work = ProjectController::viewProject($work_id))) ||
                (!$work->havePermissionOrHigher(OWNER)) || // Calling User must have OWNER permission
                (!$instance->havePermissionOrHigher(SUBMIT))) // Calling User must have WRITE permission
        {
            return false;
        }

        return $instance->submitWork($work_id, $work_is_portfolio);
    }

    public static function approveChildOfInstance($instance_id, $child_id)
    {
        
    }

    public static function rejectChildOfInstance($instance_id, $child_id)
    {
        
    }

    public static function removeChildOfInstance($instance_id, $child_id)
    {
        
    }

    public static function getPublicChildren($id)
    {
        
    }

    public static function getPrivateChildren($id)
    {
        
    }

    /**
     * 	Retrieve all children of a specific AssignmentInstance that are awaiting approval after submission.
     *
     * 	Checks current User for permission level of WRITE or above.
     *
     * 	@param	int		$id			Identifier of the AssignmentInstance whose children we seek
     *
     * 	@return	array				Associative array of the following format:
     * 								- Key = identifier of the child object awaiting approval
     * 								- Value = boolean specifying whether or not the child is a Portfolio or Project
     * 					  				(true = child is sub-Portfolio, false = child is not sub-Portfolio)
     */
    public static function getSubmittedChildren($id)
    {
        if ((!$user_id = AuthenticationController::getCurrentUserID()) ||
                (!$instance = self::getAssignmentInstance($id)) ||
                (!$instance->havePermissionOrHigher(WRITE))) // Calling User must have WRITE permission
        {
            return false;
        }

        $result = array();
        foreach ($instance->children as $child_id => $arr)
        {
            if ($arr[1] == SUBMITTED)
            {
                $result[$child_id] = $arr[0];
            }
        }

        return $result;
    }

}

?>
