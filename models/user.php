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

			case 'portfolios':
				return Model::factory('Portfolio')
							-> where('creator_id', $this->user_id)
							-> find_many();

			case 'projects':
				return Model::factory('Project')
							-> where('creator_id', $this->user_id)
							-> find_many();
			case 'groups':
				$maps = Model::factory('GroupUserMap')
				-> where('user_id', $this->user_id)
				-> find_many();

				$groups = array();

				foreach ($maps as $map)
				{
					$groups[] = Model::factory('Group')
									-> where('group_id', $map->group_id)
									-> find_one();
				}

				return $groups;
		}
	}

	/**
	 * Sets the user's deactivated flag to 1 in the database
	 */
	public function delete()
	{
		$this->deactivated = 1;
		$this->save();
	}
}

?>