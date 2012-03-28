<?php

/*
* A User class represents a single row in the REPO_Users table.
*/

class User extends Model
{
	public static $_table = "AUTH_Users";
	public static $_id_column = "user_id";

	/**
	 * @return an array containing the user's major and minor departments.
	 */
	public function departments()
	{
		$majorDept = Model::factory('Department')
						-> where('dept_id', $this->major)
						-> find_one();

		$minorDept = Model::factory('Department')
						-> where('dept_id', $this->minor)
						-> find_one();

		return array("major" => $majorDept, "minor" => $minorDept);
	}

	/**
	 * @return an array containing portfolios created by the user
	 */
	public function portfolios()
	{
		return Model::factory('Portfolio')
					-> where('creator_id', $this->user_id)
					-> find_many();
	}

	/**
	 * @return the projects created by the user
	 */
	public function projects()
	{
		return Model::factory('Project')
					-> where('creator_id', $this->user_id)
					-> find_many();
	}

	/**
	 * @return the sections that this user instructs
	 */
	public function sectionsInstructed()
	{
		return Model::factory('Section')
				-> where('instruct_id', $this->user_id)
				-> find_many();
	}

	/**
	 * @return the user's full name, determined by concantenating their first and last name
	 */
	public function fullName()
	{
		return $this->first . " " . $this->last;
	}

	/**
	 * @return the groups that this user is a member of
	 */
	public function groups()
	{
		$maps = Model::factory('GroupUserMap')
					-> where('user_id', $this->user_id)
					-> find_many();

		$groups = array();

		foreach ($maps as $map)
		{
			$groups[] = Model::factory('Group')
							-> where('group_id', $map->group_id)
							-> find_many();
		}

		return $groups;
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