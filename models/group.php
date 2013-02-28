<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');

/**
 * A Group object represents a single row in the REPO_Groups table.
 *
 * @package Models
 */
class Group extends Model
{
	public static $_table = "AUTH_Groups";
	public static $_id_column = "group_id";

	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
		case 'permissions':
			$return = array();
			$user_id = AuthenticationController::get_current_user_id();
			// If current User's ID is the owner_user_id of the Group, add ownership privilege
			if ($this->owner_user_id === $user_id)
			{
				$return[] = OWNER;
			}
			return $return;
			break;

		case 'owner':
			return UserController::getUser($this->creator_user_id);
			break;

		case 'members':
			$maps = Model::factory('GroupUserMap')
				->where('group_id', $this->id())
				->find_many();
			$members = array();
			foreach ($maps as $map)
			{
				$members[] = $map->user_id;
			}
			return $members;
			break;

		case 'count':
			return count($this->members);
			break;

		default:
			return parent::__get($name);
			break;
		}
	}

	/**
	 *	Add a User to this Group.
	 *
	 *	@param	int		$id		Identifier of the User to add to this Group
	 *
	 *	@return	bool			True if successful, false otherwise
	 */
	public function addUser($id)
	{
		if (!$map = Model::factory('GroupUserMap')->create())
		{
			return false;
		}
		$map->group_id = $this->id();
		$map->user_id = $id;

		return $map->save();
	}

	/**
	 *	Remove a User from this Group.
	 *
	 *	@param	int		$id		Identifier of the User to remove from this Group
	 *
	 *	@return	bool			True if successful, false otherwise
	 */
	public function removeUser($id)
	{
		if ((!$user = UserController::getUser($id)) ||
			(!$map = Model::factory('GroupUserMap')
			->where('group_id', $this->id())
			->where('user_id', $id)
			->find_one()))
		{
			return false;
		}

		return $map->delete();
	}

	/**
	 *	Checks to see if a User exists within this Group
	 *
	 *	@param	int		$id		Identifier of the User to check for
	 *
	 *	@return	bool			True if Group contains the specified User, false otherwise
	 */
	public function containsUser($id)
	{
		return in_array($id, $this->members, true);
	}
}

?>
