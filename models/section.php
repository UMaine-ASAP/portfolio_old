<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/access_map_model.php');
require_once('models/mappings.php');

class Section extends AccessMapModel
{
	public static $_table = "REPO_Sections";
	public static $_id_column = "section_id";
	public static $_access_map_name = "SectionAccessMap";
	public static $_access_table = "REPO_Section_access_map";

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
