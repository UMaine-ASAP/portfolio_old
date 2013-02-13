<?php
require_once('libraries/constant.php');
/*
* A User class represents a single row in the REPO_Users table.
*/

class User extends Model
{
	public static $_table = "AUTH_Users";
	public static $_id_column = "user_id";

	public function __get($name)
	{
		switch ($name)
		{
			case 'departments':
				$majorDept = Model::factory('Department')
					-> where('dept_id', $this->major)
					-> find_one();

				$minorDept = Model::factory('Department')
								-> where('dept_id', $this->minor)
								-> find_one();

				return array("major" => $majorDept, "minor" => $minorDept);
				break;

			case 'portfolios':
				$result = ORM::for_table('REPO_Portfolio_access_map')
					->table_alias('access')
					->select('access.access_type')
					->join('AUTH_Group_user_map', 'access.group_id = AUTH_Group_user_map.group_id')
					->where('access.access_type', OWNER)
					->where('AUTH_Group_user_map.user_id', $this->id())
					->find_many();
				$return = array();
				foreach ($result as $p)
				{
					$return[] = $p->access.port_id;
				}
				return $return;
				break;

			case 'projects':
				$result = ORM::for_table('REPO_Project_access_map')
					->table_alias('access')
					->select('access.access_type')
					->join('AUTH_Group_user_map', 'access.group_id = AUTH_Group_user_map.group_id')
					->where('access.access_type', OWNER)
					->where('AUTH_Group_user_map.user_id', $this->id())
					->find_many();
				$return = array();
				foreach ($result as $p)
				{
					$return[] = $p->access.proj_id;
				}
				return $return;
				break;

			case 'groups':
				$maps = Model::factory('GroupUserMap')
					->where('user_id', $this->user_id)
					->find_many();

				$groups = array();

				foreach ($maps as $map)
				{
					$groups[] = Model::factory('Group')
									->where('group_id', $map->group_id)
									->find_one();
				}
				return $groups;
				break;

			default:
				return parent::__get($name);
				break;
		}
	}

	/**
	 * Sets the user's deactivated flag to 1 in the database
	 */
	public function delete()
	{
		$this->deactivated = 1;
		return $this->save();
	}
}

