<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');

/**
 * @package Models
 */

/**
 * The object pertaining to "templates" or non-instantiated, saved Assignments 
 * by instructors or otherwise.
 */
class Assignment extends Model
{
	public static $_table = "REPO_Assignments";
	public static $_id_column = "assign_id";

	/**
	 * @return the User who created this Assignment
	 */
	public function creator()
	{
		return Model::factory('User')
					-> where('user_id', $this->creator_user_id)
					-> find_one();
	}

}

/**
 * The object pertaining to instantiated Assignment objects, assigned to 
 * a section or other Group of users.
 */
class AssignmentInstance extends Model
{
	public static $_table = "REPO_Assignment_instances";
	public static $_id_column = "instance_id";


	/**
	 * @return the User who created this AssignmentInstance
	 */
	public function creator()
	{
		return 


	/**
	 * @return the Section that this AssignmentInstance is associated with
	 */
	public function section()
	{
		return Model::factory('Section')
					-> where('section_id', $this->section_id)
					-> find_one();
	}
}

?>
