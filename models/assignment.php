<?php

class Assignment extends Model
{
	public static $_table = "REPO_Assignments";
	public static $_id_column = "assign_id";

	public function owner()
	{
		return Model::factory('User')
					-> where('user_id', $this->creator_id)
					-> find_one();
	}

	public function section()
	{
		return Model::factory('Section')
					-> where('section_id', $this->section_id)
					-> find_one();
	}
}

?>