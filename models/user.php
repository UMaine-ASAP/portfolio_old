<?php

/*
* A User class represents a single row in the REPO_Users table.
*/

class User extends Model
{
	public static $_table = "AUTH_Users";
	public static $_id_column = "user_id";

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

	public function portfolios()
	{
		return Model::factory('Portfolio')
					-> where('creator_id', $this->user_id)
					-> find_many();
	}

	public function projects()
	{
		return Model::factory('Project')
					-> where('creator_id', $this->user_id)
					-> find_many();
	}

	public function sectionsInstructed()
	{
		return Model::factory('Section')
				-> where('instruct_id', $this->user_id)
				-> find_many();
	}

	public function sectionsOwned()
	{
		return Model::factory('Section')
				-> where('instruct_id', $this->user_id)
				-> find_many();
	}

	public function fullName()
	{
		return $this->first . " " . $this->last;
	}

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

	public function delete()
	{
		$this->deactivated = 1;
		$this->save();
	}
}

?>