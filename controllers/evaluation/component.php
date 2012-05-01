<?php

// Libraries and Settings
require_once('libraries/constant.php');
require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');

// Models
require_once('models/evaluation/component.php');
require_once('models/evaluation/mappings.php');

// Controllers
require_once('controllers/authentication.php');

/**
 * Component controller.
 *
 * @package Controllers
 */
class ComponentController
{
	/************************************************************************************
	 * Component object management														*
	 ***********************************************************************************/

	/**
	 *	Creates a new Component object in the system.
	 *
	 *  @param 	string|null		$question			Plain-text question (2^16 character limit, optional)
	 *  @param 	int|null 		$type 				Identifier of the type of this component
	 *  @param 	string|null 	$options 			Plain-text description of the Form (2^16 character limit, optional)
	 *  @param 	bool|null 		$isRequired			Indicates whether this field is required or not (optional)
	 *  @param 	int|null 		$weight				Determines the position of this component relative to other components through their weight values (optional)
	 *  @param 	bool|null		$isPrivate			Indicates whether this component is only accessible to specified users/groups or is publicly available (optional)
	 *
	 *	@return	object|bool								The created Component object if successful, false otherwise
	 */
	public static function createComponent($question = NULL, $type = NULL, $options = NULL, $isRequired = NULL, $weight = NULL, $category = NULL, $isPrivate = NULL)
	{

		if ( (!$user_id = AuthenticationController::get_current_user_id()) ||
			 (!$component = Model::factory('Component')->create()) )
		{
			return false;
		}

		$component->question 		= $question;
		$component->type 			= $type;
		$component->options 		= $options;
		$component->required 		= $isRequired;
		$component->weight 			= $weight;
		$component->category 		= $category;
		$component->private 		= $isPrivate;
		$component->creator_user_id = $user_id->id();

		if (!$component->save())
		{
			$component->destroy();		// Assume these succeed
			return false;
		}

		return $component;
	}

	/**
	 *	Edit a specific Component object
	 *
	 *	@param	int|null		$id					Identifier of the Component object to edit
	 *  @param 	string|null		$question			Plain-text question (2^16 character limit, optional)
	 *  @param 	int|null 		$type 				Identifier of the type of this component
	 *  @param 	string|null 	$options 			Plain-text description of the Form (2^16 character limit, optional)
	 *  @param 	bool|null 		$isRequired			Indicates whether this field is required or not (optional)
	 *  @param 	int|null 		$weight				Determines the position of this component relative to other components through their weight values (optional)
	 *  @param 	bool|null		$isPrivate			Indicates whether this component is only accessible to specified users/groups or is publicly available (optional)
	 *  @param 	int|null		$creator_user_id	Identifier of the User who created this component (optional)
	 *
	 *	@return	bool								True if successful, false otherwise
	 */
	public static function editComponent($id, $question = NULL, $type = NULL, $options = NULL, $isRequired = NULL, $weight = NULL, $category = NULL, $isPrivate = NULL, $creator_user_id = NULL)
	{
		//@TODO: authentication check!!! access to component????

		if ( (!$user_id = AuthenticationController::get_current_user_id()) ||
			 (!$component = self::getComponent($id)) )
		{
			return false;
		}

		// Check permissions for editing the component here
		// $component->permissions

		if (!is_null($creator_user_id))
		{
			// Check for ownership privileges here
			$component->creator_user_id = $creator_user_id;
		}

		if (!is_null($question))	{ $component->question 	= $question; }
		if (!is_null($type))		{ $component->type 		= $type; }
		if (!is_null($options)) 	{ $component->options 	= $options; }
		if (!is_null($isRequired)) 	{ $component->required 	= $isRequired; }
		if (!is_null($weight)) 		{ $component->weight 	= $weight; }
		if (!is_null($category)) 	{ $component->category 	= $category; }
		if (!is_null($isPrivate)) 	{ $component->private 	= $isPrivate; }

		return $component->save();
	}

	/**
	 *	Gets a specific Component object for private use.
	 *
	 *	Does not check permissions.
	 *
	 *	@param	int		$id		Identifier of the Component object to get
	 *
	 *	@return	object|bool		The Component object if found, false otherwise
	 */
	public static function viewComponent($id)
	{
		//@TODO: permission check
		return self::getComponent($id);

	}

	/**
	 *	Gets a specific Component object for private use.
	 *
	 *	Does not check permissions.
	 *
	 *	@param	int		$id		Identifier of the Component object to get
	 *
	 *	@return	object|bool		The Component object if found, false otherwise
	 */
	private static function getComponent($id)
	{
		return Model::factory('Component')->find_one($id);
	}
}

