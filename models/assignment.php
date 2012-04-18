<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/mappings.php');
require_once('controllers/user.php');
require_once('controllers/section.php');
require_once('controllers/portfolio.php');

/**
 * @package Models
 */

/**
 *	The object pertaining to "templates" or non-instantiated, saved Assignments 
 *	by instructors or otherwise.
 *	
 *	@property-read	array	permissions		Array of permission levels specific to the requesting user
 *	@property-read	object	owner			User object of the User who currently owns the Assignment
 */
class Assignment extends Model
{
	public static $_table = "REPO_Assignments";
	public static $_id_column = "assign_id";

	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'permissions':
			$return = array();
			// If current User's ID is the owner_user_id of the Portfolio, add ownership privilege
			if ($this->owner_user_id == USER_ID)	// Check user ID here
			{
				$return[] = OWNER;
				return $return;
			}
			else
			{
				$result = ORM::for_table('REPO_Assignment_access_map')
					->table_alias('access')
					->select('access.access_type')
					->join('AUTH_Group_user_map', 'access.group_id = AUTH_Group_user_map.group_id')
					->where('access.port_id', $this->id())
					->where('AUTH_Group_user_map.user_id', USER_ID)	// add user credentials here
					->find_many();
				
				foreach ($result as $perm)
				{	// Results are returned as ORM objects, de-reference them
					$return[] = $perm->access_type;
				}
				return $return;
			}
			break;

		case 'owner':
			return UserController::getUser($this->creator_user_id);
			break;

		default:
			return parent::__get($name);
			break;
		}
	}

	/**
	 *	Overridden delete function to handle the deactivation of this Assignment.
	 *
	 *	@return	bool		True if successful, false otherwise
	 */
	public function delete()
	{
		$this->deactivated = true;
		return $this->save();
	}

	/**
	 *	Allow for un-deletion of previously deactivated Assignments.
	 *
	 *	@return	bool		True if successful, false otherwise
	 */
	public function unDelete()
	{
		$this->deactivated = false;
		return $this->save();
	}

	/**
	 *	Allow for the complete removal of the Assignment and all instantiations of it from the system.
	 *
	 *	@return	bool		True if successful, false otherwise
	 */
	public function destroy()
	{

	}

}

/**
 *	The object pertaining to instantiated Assignment objects, assigned to 
 *	a section or other Group of users.
 *	
 *	@property-read	array	permissions		Array of permission levels specific to the requesting user
 *	@property-read	object	owner			User object of the User who currently owns the AssignmentInstance
 *	@property-read	object	section			Section object the Assignment has been instantiated for
 *	@property-read	object	portfolio		Portfolio object the Assignment's works are contained within
 */
class AssignmentInstance extends Model
{
	public static $_table = "REPO_Assignment_instances";
	public static $_id_column = "instance_id";

	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'permissions':
			$return = array();
			// If curret User's ID is the owner_user_id of the Portfolio, add ownership privilege
			if ($this->owner_user_id == USER_ID)	// Check user ID here
			{
				$return[] = OWNER;
				return $return;
			}
			else
			{
				$result = ORM::for_table('REPO_Portfolio_access_map')
					->table_alias('access')
					->select('access.access_type')
					->join('AUTH_Group_user_map', 'access.group_id = AUTH_Group_user_map.group_id')
					->where('access.port_id', $this->id())
					->where('AUTH_Group_user_map.user_id', USER_ID)	// add user credentials here
					->find_many();
				
				foreach ($result as $perm)
				{	// Results are returned as ORM objects, de-reference them
					$return[] = $perm->access_type;
				}
				return $return;
			}
			break;

		case 'owner':
			return UserController::getUser($this->owner_user_id);
			break;

		case 'section':
			return SectionController::getSection($this->section_id);
			break;

		case 'portfolio':
			return PortfolioController::viewPortfolio($this->portfolio_id);
			break;

		default:
			parent::__get($name);
			break;
		}
	}

}

?>
