<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');

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
	 *	@param 	bool 		$private			The privacy of the Media object (true=private)
	 *	@param	string		$filename			Filename where the Media is stored on the server (2^16 character max)
	 *
	 *	@return object|bool						The created Media object if successful, false otherwise.
	 */
	static function createMedia($type, $title, $description, $private, $filename)
	{
		// Check for creation privileges

		if (!$media = Model::factory('Media')->create())
		{
			return false;
		}

		$media->creator_user_id = USER_ID;	// Get USER ID from caller user
		$media->title = $title;
		$media->description = $description;
		$media->private = $private;
		$media->filename = $filename;

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
	 * 	@param 	int		$id				The ID of the media being edited
	 *	@param 	string	$abstract		The abstract of the media. The abstract will not be changed if an empty string is passed.
	 *	@param 	string	$description	The description of the media. The description will not be changed if an empty string is passed
	 *	@param 	bool	$private		The media's private (TRUE for private, FALSE for public)
	 *
	 *	@return bool					True if the media was successfully edited, false otherwise
	 */
	static function editMedia($id, $abstract = NULL, $description = NULL, $private = NULL)
	{
		if (!$media = self::getMedia($id))
		{
			return false;
		}

		// Check for editing permissions

		if (!is_null($abstract))	{ $media->abstract = $abstract;	}
		if (!is_null($description))	{ $media->description = $description; }
		if (!is_null($media))		{ $media->private = $private; }

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
		if (!$media = self::getMedia($id))
		{
			return false;
		}

		// Check for deletion permissions

		return $media->delete();
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
