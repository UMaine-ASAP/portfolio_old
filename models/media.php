<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('controllers/project.php');

/**
 * A Media object represents a single row in the REPO_Media table.
 *
 * @package Models
 */
class Media extends Model
{
	public static $_table = "REPO_Media";
	public static $_id_column = "media_id";

	public function __get($name)
	{
		switch ($name)
		{
			case 'projects':
				if (!$maps = Model::factory('ProjectMediaMap')
					->where('media_id', $this->media_id)
					->find_many())
				{
					return false;
				}

				$projects = array();

				foreach ($maps as $map)
				{
					if ($project = ProjectController::viewProject($map->proj_id))	// check for user's viewing privilege on the project
					{
						$projects[] = $project;
					}
				}
				return $projects;
				break;

			default:
				return parent::__get($name);
				break;
		}
	}
	/**
	 *	Acquire a handle to the file specified by the media object's filename.
	 *		@param mode|string The mode of the handle. Defaults to read-only.
	 *		@return The handle to the file, or false if the file could not be opened.
	 */
	public function handle($mode = 'r')
	{
		//TODO: check to see if the user that's currently logged in can see this file, or however we're going to manage that

		if (!$this->private) return fopen($this->filename, $mode);
	}
	
	/**
	 *	Retrieve all Groups with permissions for this Media.
	 *
	 *	@return	array				An array of arrays with keys specifying the identifier of the Group,
	 *								and the value for the key being an array of all permission levels
	 *								of the Group for this Media, as specified in 'constant.php'.
	 */
	public function groupsWithPermission()
	{
		$result = Model::Factory('MediaAccessMap')
			->where('media_id', $this->id())
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
	 *	Retrieve a Group's permissions for this Media.
	 *
	 *	@param	int		$group		The identifier of the Group object for which we seek the permissions of.
	 *
	 *	@return	array				An array of permission levels as specified in 'constant.php', or an empty
	 * 								array in the event the Group has no permissions.
	 */
	public function permissionsForGroup($group_id)
	{
		$result = Model::factory('MediaAccessMap')
			->where('media_id', $this->id())
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
	 *	Add a permission level for a Group in regards to this Media.
	 *
	 *	@param	int		$group		The identifier of the Group objects we seek to add permissions to.
	 *	@param	int		$perm		The permission level as specified in 'constant.php' for the Group.
	 *
	 *	@return	bool				True if successful, false otherwise.
	 */
	public function addPermissionForGroup($group_id, $perm_id)
	{
		if (Model::factory('MediaAccessMap')
			->where('media_id', $this->id())
			->where('group_id', $group_id)
			->where('access_type', $perm_id)
			->find_one())
		{
			return false;
		}

		if (!$map = Model::factory('MediaAccessMap')->create())
		{
			return false;
		}
		
		$map->media_id = $this->id();
		$map->group_id = $group_id;
		$map->access_type = $perm_id;

		return $map->save();
	}

	/**
	 *	Remove a permission level for a Group in regards to this Media.
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
				$permORM = Model::factory('MediaAccessMap')
					->where('media_id', $this->id())
					->where('group_id', $group_id)
					->where('access_type', $perm_id)
					->find_one();
				return $permORM->delete();
			}
		}

		return false;
	}

	/**
	 *	Add a permission level for a specific User in regards to this Media.
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
	 *	Remove a permission level for a specific User in regards to this Media.
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
