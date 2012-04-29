<?php

require_once(__DIR__ . '/../../libraries/Idiorm/idiorm.php');
require_once(__DIR__ . '/../../libraries/Paris/paris.php');

class FormComponentMap extends Model
{
	public static $_table = 'EVAL_Form_component_map';
	public static $_id_column = 'id';

	private static function getFormComponentMap($form_id)
	{
		return Model::factory('FormComponentMap')->where('form_id', $form_id)->find_one();	
	}

	/**
	 * Returns all the components associated with a given form.
	 * 
	 * @param 	int 			$form_id 	Identifier of the form to get elements of.
	 * 
	 * @return 	objects|bool 				An array of components associated with the form
	 */
	public static function getComponentsFromForm($form_id)
	{
		$formComponentMap = FormComponentMap::getFormComponentMap($form_id);
		if( !($formComponentMap instanceof FormComponentMap) ) {
			return false;
		}
		return $formComponentMap->Components();
	}

	public function Form()
	{
		return Model::factory('Form')->find_one($this->form_id);
	}

	public function Components()
	{
		return Model::factory('Component')->find_many($this->component_id);
	}
}
