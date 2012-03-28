<?php

require_once(__DIR__ . '/../libraries/Idiorm/idiorm.php');
require_once(__DIR__ . '/../libraries/Paris/paris.php');
require_once(__DIR__ . '/../models/mappings.php');
include_once(__DIR__ . '/../libraries/constant.php');

DEFINE("USER_ID", 2);

/**
 *	Portfolio model object
 *
 *	A Portfolio object represents a single row in the REPO_Portfolios table, along with
 *	all associated data derived from its relations.
 *
 *	@property-read	array	$permissions	Array of permission levels specific to the requesting user.
 *											Each value corresponds to an enumerable value as specified in 'constants.php'.
 *	@property-read	array	$children		Array of tuples(arrays) specifying children Portfolio or Project objects underneath the current one.
 *											Tuples consist of:
 *												- Identifier of child Portfolio / Project object at index 0.
 *												- Boolean value specifying whether the child is a sub-Portfolio or Project
 *												  (true = child is sub-Portfolio, false = child is not sub-Portfolio) at index 1.
 *
 * 	@package Models
 */
class Portfolio extends Model
{
	public static $_table = 'REPO_Portfolios';
	public static $_id_column = 'port_id';


	/**
	 * Magic-method property getters
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
				->where('AUTH_Group_user_map.user_id', USER_ID)
				->find_many();
			$return = array();
			foreach ($result as $perm)
			{	// Results are returned as ORM objects, de-reference them
				$return[] = $perm->access_type;
			}
			return $return;
			break;
		
		case 'children':
			$result = ORM::for_table('REPO_Portfolio_project_map')
				->table_alias('map')
				->where('map.port_id', $this->id())
				->find_many();
			$return = array();
			foreach ($result as $child)
			{	// De-reference ORM object
				$return[] = array($child->child_id, $child->child_is_portfolio);
			}
			return $return;
			break;

		default:
			parent::__get($name);
			break;
		}
	}

	/**
	 * Magic-method property setters
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
		case 'permissions':
			// Permissions are read-only with magic-methods
			return false;
			break;

		case 'children':
			// Children are read-only with magic-methods
			return false;
			break;

		default:
			parent::__set($name, $value);
			break;
		}
	}

	/**
	 * Overridden delete function to handle the removal of all permissions for this Portfolio.
	 */
	public function delete()
	{
		// Remove all Groups' permissions on this Portfolio
		$groups = $this->groupsWithPermission();
		foreach ($groups as $group=>$permissions)
		{
			foreach ($permissions as $perm)
			{
				// Delete
			}
		}
		
		parent::delete();
	}

	/**
	 * Retrieve all Groups with permissions for this Porfolio.
	 *
	 * @return	array				An array of arrays with keys specifying the identifier of the Group,
	 *								and the value for the key being an array of all permission levels
	 *								of the Group for this Portfolio, as specified in 'constant.php'.
	 */
	public function groupsWithPermission()
	{
		$result = ORM::for_table('REPO_Portfolio_access_map')
			->table_alias('access')
			->where('access.port_id', $this->id())
			->find_many();
		$return = array();
		foreach ($result as $perm)
		{
			if (isset($return[$perm->group_id]))
			{
				$return[$perm->group_id][] = $perm->access_type;
			}
			else
			{
				$return[$perm->group_id] = array($perm->access_type);
			}
		}
		return $return;
	}

	/**
	 * Retrieve a Group's permissions for this Portfolio.
	 *
	 * @param	int		$group		The identifier of the Group object for which we seek the permissions of.
	 *
	 * @return	array				An array of permission levels as specified in 'constant.php', or an empty
	 * 								array in the event the Group has no permissions.
	 */
	public function permissionsForGroup($group)
	{
		$result = ORM::for_table('REPO_Portfolio_access_map')
			->table_alias('access')
			->select('access.access_type')
			->where('access.port_id', $this->id())
			->where('access.group_id', $group)
			->find_many();
		$return = array();
		foreach($result as $perm)
		{	// De-reference results into raw array
			$return[] = $perm->access_type;
		}
		return $return;
	}
}

?>
