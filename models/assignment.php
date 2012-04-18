<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('controllers/user.php');
require_once('controllers/section.php');

/**
 * @package Models
 */

/**
 * The object pertaining to "templates" or non-instantiated, saved Assignments 
 * by instructors or otherwise.
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
			$result = ORM::for_table('REPO_Portfolio_access_map')
				->table_alias('access')
				->select('access.access_type')
				->join('AUTH_Group_user_map', 'access.group_id = AUTH_Group_user_map.group_id')
				->where('access.port_id', $this->id())
				->where('AUTH_Group_user_map.user_id', USER_ID)	// add user credentials here
				->find_many();
			
			$return = array();
			foreach ($result as $perm)
			{	// Results are returned as ORM objects, de-reference them
				$return[] = $perm->access_type;
			}
			// If curret User's ID is the owner_user_id of the Portfolio, add ownership privilege
			return $return;
			break;

		case 'creator':
			break;

		default:
			parent::__get($name);
			break;
		}
	}
	/**
	 * @return the User who created this Assignment
	 */
	public function creator()
	{
		return UserController::getUser($this->creator_user_id);
	}

}

/**
 * The object pertaining to instantiated Assignment objects, assigned to 
 * a section or other Group of users.
 */
class AssignmentInstance extends Model
{
	public static $_table = "REPO_Assignment_instances";
	public static $_id_column = "instance_id";


	/**
	 * @return the User who created this AssignmentInstance
	 */
	public function creator()
	{
		return UserController::getUser($this->creator_user_id);
	}

	/**
	 * @return the Section that this AssignmentInstance is associated with
	 */
	public function section()
	{
		return SectionController::getSection($this->section_id);
	}
}

?>
