<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('models/section.php');

/**
 *	Section controller.
 *
 *	@package Controllers
 */
class SectionController
{

	public static function getSection($sectionID)
	{
		return Model::factory('Section')->find_one($sectionID);
	}
}

?>
