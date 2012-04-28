<?php

require_once(__DIR__ . '/../libraries/Idiorm/idiorm.php');
require_once(__DIR__ . '/../libraries/Paris/paris.php');

class FormComponentMap extends Model
{
	public static $_table = 'EVAL_Form_component_map';
	public static $_id_column = 'id';

	public function Form()
	{
		return Model::factory('Form')->find_one($this->form_id);
	}

	public function Component()
	{
		return Model::factory('Component')->find_one($this->component_id);
	}
}
