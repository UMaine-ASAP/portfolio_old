<?php

/*
* A User class represents a single row in the REPO_Users table.
*
*/

class User extends Model
{
	public static $_table = "REPO_Users";
	public static $_id_column = "user_id";
}

?>