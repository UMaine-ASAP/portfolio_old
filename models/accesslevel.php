<?php
require_once(__DIR__ . '/../libraries/Idiorm/idiorm.php');
require_once(__DIR__ . '/../libraries/Paris/paris.php');

class AccessLevel extends Model
{
	public static $_table = "REPO_Access_levels";
	public static $_id_column = "access_id";
}

?>
