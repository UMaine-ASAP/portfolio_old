<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');

/**
 * A Group object represents a single row in the REPO_Groups table.
 *
 * @package Models
 */
class Group extends Model
{
	public static $_table = "AUTH_Groups";
	public static $_id_column = "group_id";
}

?>
