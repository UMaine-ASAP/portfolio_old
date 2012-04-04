<?php

/**
* A Project object represents a single row in the REPO_Projects table.
*/

class Project extends Model
{
	public static $_table = 'REPO_Projects';
	public static $_id_column = 'proj_id';

	/**
	 * Gets the content_media_map of the project.
	 *
	 *	@return the project_media_map if found, otherwise false
	 */
	public function mediaMap()
	{
		return Model::factory('ProjectMediaMap')->find_one($this->id());
	}

	/**
	 *	Gets all media associated with the project.
	 *	
	 *	@return an array containing Media objects (empty if none were found)
	 */
	public function media()
	{
		return Model::factory('Media')
				-> where('content_id', $this->mediaMap()->content_id)
				-> find_many();
	}

	/**
	 * Retrieve the User who created a Project.
	 *
	 *	@return the user who created the project if found, otherwise false
	 */
	public function creator()
	{
		return Model::factory('User')
					-> where ('user_id', $this->creator_user_id)
					-> find_one();
	}
}

?>
