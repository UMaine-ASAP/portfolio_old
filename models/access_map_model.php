<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/mappings.php');

/**
 *	Model superclass that contains boiler-plate code for managing AccessMaps
 *
 *	NOTE: Class will use the Paris class variable 'static::$_id_column' as the id column
 *	of the object in the specified mapping Paris object.
 *
 *	@package Models
 */
class AccessMapModel extends Model
{
	/**
	 *	This is the name of the Paris object that maps access to the Model object.
	 *	For example, Portfolios' mapping is called static::$_access_map_name.
	 */
	public static $_access_map_name;
	/**
	 *	Name of the table the _access_map_name corresponds to
	 */
	public static $_access_table;


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'permissions':
			if (!$user_id = AuthenticationController::get_current_user_id())
			{
				return false;
			}

			$return = array();
			$result = ORM::for_table(static::$_access_table)
				->table_alias('access')
				->select('access.access_type')
				->join('AUTH_Group_user_map', 'access.group_id = AUTH_Group_user_map.group_id')
				->where('access.' . static::$_id_column, $this->id())
				->where('AUTH_Group_user_map.user_id', $user_id)	// add user credentials here
				->find_many();
			
			foreach ($result as $perm)
			{	// Results are returned as ORM objects, de-reference them
				$return[] = $perm->access_type;
			}
			return $return;
			break;
		
		default:
			return parent::__get($name);
			break;
		}
	}

	/**
	 *	Checks to see whether the current User has a permission level equal to or higher
	 *	than the one specified on this Model object.
	 *
	 *	@param	int		$perm		Identifier of the permission level to check for
	 *
	 *	@return	bool				True if current User has permission, false otherwise
	 */
	public function havePermissionOrHigher($perm)
	{
		return array_reduce($this->permissions, function($reduced, $p) {
			return ($reduced || ($p <= $perm));
		});
	}

	/**
	 *	Retrieve all Groups with permissions for this Model object.
	 *
	 *	@return	array				An array of arrays with keys specifying the identifier of the Group,
	 *								and the value for the key being an array of all permission levels
	 *								of the Group for this Model object, as specified in 'constant.php'.
	 */
	public function groupsWithPermission()
	{
		$result = Model::Factory(static::$_access_map_name)
			->where(static::$_id_column, $this->id())
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
	 *	Retrieve a Group's permissions for this Model object.
	 *
	 *	@param	int		$group		The identifier of the Group object for which we seek the permissions of.
	 *
	 *	@return	array				An array of permission levels as specified in 'constant.php', or an empty
	 * 								array in the event the Group has no permissions.
	 */
	public function permissionsForGroup($group_id)
	{
		$result = Model::factory(static::$_access_map_name)
			->where(static::$_id_column, $this->id())
			->where('group_id', $group_id)
			->find_many();
		
		$return = array();
		foreach($result as $perm)
		{	// De-reference ORM results into raw array
			$return[] = $perm->access_type;
		}
		return $return;
	}

	/**
	 *	Add a permission level for a Group in regards to this Model object.
	 *
	 *	@param	int		$group		The identifier of the Group objects we seek to add permissions to.
	 *	@param	int		$perm		The permission level as specified in 'constant.php' for the Group.
	 *
	 *	@return	bool				True if successful, false otherwise.
	 */
	public function addPermissionForGroup($group_id, $perm_id)
	{
		if (Model::factory(static::$_access_map_name)
			->where(static::$_id_column, $this->id())
			->where('group_id', $group_id)
			->where('access_type', $perm_id)
			->find_one())
		{
			return false;
		}

		if (!$map = Model::factory(static::$_access_map_name)->create())
		{
			return false;
		}
		
		$map->port_id = $this->id();
		$map->group_id = $group_id;
		$map->access_type = $perm_id;

		return $map->save();
	}

	/**
	 *	Remove a permission level for a Group in regards to this Model object.
	 *
	 *	@param	int		$group		The identifier of the Group objects we seek to remove permissions from.
	 *	@param	int		$perm		The permission level as specified in 'constant.php' for the Group.
	 *
	 *	@return	bool				True if successful, false otherwise.
	 */
	public function removePermissionForGroup($group_id, $perm_id)
	{
		if (!$permissions = $this->permissionsForGroup($group_id))
		{
			return false;
		}

		foreach ($permissions as $p)
		{
			if ($p == $perm_id)
			{
				$permORM = Model::factory(static::$_access_map_name)
					->where(static::$_id_column, $this->id())
					->where('group_id', $group_id)
					->where('access_type', $perm_id)
					->find_one();
				return $permORM->delete();
			}
		}

		return false;
	}

	/**
	 *	Add a permission level for a specific User in regards to this Model object.
	 *
	 *	@param	int		$user_id	Identifier of the User to add permissions for
	 *	@param	int		$perm_id	Identifier of the permission level to add for the User
	 *
	 *	@return	bool				True if successful, false otherwise
	 */
	public function addPermissionForUser($user_id, $perm_id)
	{
		if (!$group = GroupController::createGroup($this->title . " Permissions", "Permissions for " . $this->title, true))
		{
			return false;
		}

		return $this->addPermissionForGroup($group->id(), $perm_id);
	}

	/**
	 *	Remove a permission level for a specific User in regards to this Model object.
	 *
	 *	We assume that we may only remove a single User's permissions if
	 *	they are in a Group by themselves with the specific permission, and
	 *	we assume that this is the only Group the User is a member of that has this permission.
	 *	(Because of these, this should only be used to reverse the 'addPermissionForUser' above,
	 *	OR be redone in a more robust way. (Sorry)
	 *
	 *	@param	int		$user_id	Identifier of the User to remove permissions from
	 *	@param	int		$perm_id	Identifier of the permission level to remove from the User
	 *
	 *	@return	bool				True if successful, false otherwise
	 */
	public function removePermissionForUser($user_id, $perm_id)
	{
		if (!$groups = $this->groupsWithPermission())
		{
			return false;
		}

		foreach ($groups as $group_id=>$perms)
		{
			if (($group = GroupController::viewGroup($group_id)) &&
				($group->count === 1) &&
				($group->containsUser($user_id)) &&
				(in_array($perm_id, $perms)))
			{
				return $this->removePermissionForGroup($group_id, $perm_id);
			}
		}

		return false;
	}

}


?>
