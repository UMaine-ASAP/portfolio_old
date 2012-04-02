<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');

/**
 * A Media object represents a single row in the REPO_Media table.
 *
 * @package Models
 */
class Media extends Model
{
	public static $_table = "REPO_Media";
	public static $_id_column = "media_id";
}

?>
