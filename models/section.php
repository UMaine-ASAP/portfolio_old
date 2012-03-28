<?php

class Section extends Model
{
	public static $_table = 'REPO_Sections';
	public static $_id_column = 'section_id';

	/**
	 * @return the group that owns this section
	 */
	public function owner()
	{
		return Model::factory('Group')
					-> where('group_id', $this->owner_id)
					-> find_one();
	}

	/**
	 * @return the user who instructs this section
	 */
	public function instructor()
	{
		return Model::factory('User')
					-> where('user_id', $this->instruct_id)
					-> find_many();
	}

	/**
	 * @return the classes in this section
	 */
	public function classes()
	{
		return Model::factory('ClassModel')
					-> where('class_id', $this->class_id)
					-> find_many();
	}
}

?>