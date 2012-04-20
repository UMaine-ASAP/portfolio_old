<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/class.php');

/**
 * Class controller.
 *
 * @package Controllers
 */
class ClassController
{


	public static function getClass($id)
	{
		return Model::factory('ClassModel')->find_one($id);
	}

}


?>
