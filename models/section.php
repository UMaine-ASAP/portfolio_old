<?php

class Section extends Model
{
	public static $_table = "REPO_Sections";
	public static $_id_column = "section_id";

	public function days()
	{
		return ORM::for_table('REPO_Day_schedules')
					->where('sched_id', $this->day_sched)
					->find_one();
	}

	public function classes()
	{
		return Model::factory('ClassModel')
					->where('class_id', $this->class_id)
					->find_many();
	}

}

?>
