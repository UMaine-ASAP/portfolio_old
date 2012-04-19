<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('controllers/assignment.php');

/**
 * Media controller.
 *
 * @package Controllers
 */
class MediaController
{
	/**
	 *	Create a new Media object.
	 *
	 *	Calling User must exist.
	 *
	 *	@param	int			$type				Identifier of the MediaType of this Media object
	 *	@param 	string 		$title				The title of the Media object (255 character max)
	 *	@param 	string|null	$description		The description of the Media object (2^16 character max, optional)
	 *	@param	string		$filename			Filename where the Media is stored on the server (2^16 character max)
	 *
	 *	@return object|bool						The created Media object if successful, false otherwise.
	 */
	static function createMedia($type, $title, $description, $filename)
	{
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}

		if (!$media = Model::factory('Media')->create())
		{
			return false;
		}

		$media->creator_user_id = $user->user_id;	// Get USER ID from caller user
		$media->title = $title;
		$media->description = $description;
		$media->filename = $filename;
		$media->created = date("Y-m-d H:i:s");

		if (!$media->save())
		{
			$media->delete();	// Assume this succeeds
			return false;
		}

		return $media;
	}

	/**
	 *	Edit a specific Media object's properties.
	 *
	 *	The currently logged-in user must have editing permissions.
	 *
	 * 	@param 	int		$id				The identifier of the Media being edited
	 * 	@param	int		$type			Identifier of the MediaType of the Media being edited
	 * 	@param	string	$title			Title of the Media (255 character max)
	 *	@param 	string	$description	The description of the Media. The description will not be changed if an empty string is passed
	 *									(2^16 character max)
	 *	@param	string	$filename		The path where the media file is stored (2^16 character max)
	 *
	 *	@return bool					True if the media was successfully edited, false otherwise
	 */
	static function editMedia($id, $type = NULL, $title = NULL, $description = NULL, $filename = NULL)
	{
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}

		if (!$media = self::getMedia($id))
		{
			return false;
		}

		//Check for editing permissions

		if (!is_null($type))		{ $media->type = $type; }
		if (!is_null($title))		{ $media->title = $title; }
		if (!is_null($description))	{ $media->description = $description; }
		if (!is_null($filename))	{ $media->filename = $filename; }
		$media->edited = date("Y-m-d H:i:s");

		return $media->save();
	}

	/**
	 *	Deletes a specific Media object.
	 *
	 *	The calling User must have deletion permissions.
	 *
	 *	@param	int 	$id 	The ID of the media to delete
	 *
	 *	@return bool			True if the media was successfully deleted, otherwise false
	 */
	static function deleteMedia($id)
	{
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}
		if (!$media = self::getMedia($id))
		{
			return false;
		}

		if ($user->user_id === $media->creator_user_id)
		{
			return $media->delete();
		}
	}

	/**
	 *	Retrieves a Media for viewing by the caller.
	 *	
	 *	Caller must have viewing permissions for the Media.
	 *
	 *	@param	int		$id		The ID of the media to view.
	 *	
	 *	@return object|bool		The Media object if successful, false otherwise.
	 */
	static function viewMedia($id)
	{
		if (!$user = AuthenticationController::get_current_user())
		{
			return false;
		}
		
		if (!$media = self::getMedia($id))
		{
			return false;
		}

		// Check for viewing permissions
		
		return $media;
	}


	/**
	 *	Retrieve a specific Media object.
	 *	
	 *	@param	int 		$id 	The ID of the media to retrieve.
	 *
	 *	@return object|bool			The Media object requested if found, otherwise false
	 */
	private static function getMedia($id)
	{
		return Model::factory('Media')->find_one($id);
	}
}

?>
