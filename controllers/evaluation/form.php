<?php

// Libraries and Settings
require_once('libraries/constant.php');
require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');

// Models
require_once('models/evaluation/form.php');
require_once('models/evaluation/mappings.php');

// Controllers
require_once('controllers/authentication.php');

/**
 * Form controller.
 *
 * @package Controllers
 */
class FormController
{
	/************************************************************************************
	 * Form object management															*
	 ***********************************************************************************/

	/**
	 *	Creates a new Form object in the system.
	 *
	 *	@param	string			$title			Plain-text title of the Form (255 character limit)
	 *	@param	string|null		$description	Plain-text description of the Form (2^16 character limit, optional)
	 *  @param  bool 			$private		Indicates whether or not this form should be inaccessible to others (optional - defaults to true)
	 *
	 *	@return object|bool					The created Form object if successful, false otherwise
 	 */
	public static function createForm($title, $description, $private = true)
	{
		if ((!$user_id = AuthenticationController::get_current_user_id()) ||
			(!$form = Model::factory('Form')->create()))
		{
			return false;
		}
			
		$form->type 		= null;
		$form->name 		= $title;
		$form->description 	= $description;	
		//$form->created 	= date();
		$form->private 		= $private;

		// Add current User as OWNER
		$form->creator_user_id = $user_id->id();

		if (!$form->save())
		{
			$form->destroy();		// Assume these succeed
			return false;
		}

		return $form;
	}

	/**
	 *	Add an existing component to the form
	 *
	 *  @param 	int 	$form_id 			Identifier of the Form object to add the component to
	 *	@param	int		$component_id		Identifier of the Component object to add to the form
	 *
	 *	@return	bool						True if component is added successfully, false otherwise
	 */	
	public static function addComponent($form_id, $component_id)
	{
		if ( (!$formComponentMap = Model::factory('FormComponentMap')->create()) ) {
			return false;
		}

		$formComponentMap->form_id 		= $form_id;
		$formComponentMap->component_id = $component_id;

		if (!$formComponentMap->save()) {
			return false;
		}
		return true;
	}

	/**
	 *	Gets a specific Form object for private use.
	 *
	 *	Does not check permissions.
	 *
	 *	@param	int		$id		Identifier of the Form object to get
	 *
	 *	@return	object|bool		The Form object if found, false otherwise
	 */
	private static function getForm($id)
	{
		return Model::factory('Form')->find_one($id);
	}

	/**
	 *	Gets a specific Quiz object.
	 *
	 *	Does not check permissions.
	 *
	 *	@param	int		$form_id	Identifier of the Form object to get
	 *
	 *	@return	object|bool			The Quiz object if available, false otherwise
	 */
	public static function buildQuiz($form_id)
	{
		$components = FormComponentMap::getComponentsFromForm($form_id);
		return $components;
	}

}

