<?php

/**
 * @package Models
 */
class Assignment extends Model
{
	public static $_table = "REPO_Assignments";
	public static $_id_column = "assign_id";

	/**
	 * @return the user who created this assignment
	 */
	public function creator()
	{
		return Model::factory('User')
					-> where('user_id', $this->creator_id)
					-> find_one();
	}

	/**
	 * @return the section that this assignment belongs to
	 */
	public function section()
	{
		return Model::factory('Section')
					-> where('section_id', $this->section_id)
					-> find_one();
	}
}

?>