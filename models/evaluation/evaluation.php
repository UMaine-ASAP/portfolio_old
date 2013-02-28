<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');

require_once('models/evaluation/mappings.php');
require_once('models/evaluation/score.php');

/**
 * An Evaluation object represents a single row in the REPO_Evaluations table.
 *
 * @package Models
 */

class Evaluation extends Model
{
	public static $_table = "EVAL_Evaluations";
	public static $_id_column = "evaluation_id";


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'id':
				return $this->evaluation_id;
			case 'status':
				$result = Model::factory('EvaluationStatus')
					->where('status_id', parent::__get("status"))
					->find_one();
				return $result;
			break;
			case 'scores':
				if( $this->status->name == 'complete' )
				{
					return Model::factory('Score')->where('evaluation_id', $this->id)->find_many();
				}
				return false;
			default:
				return parent::__get($name);
				break;
		}
	}

	/**
	 * Update evaluation status
	 * 
	 * @param 	int 		$status_id 		Identifier of the status to use
	 * 
	 * @return 	bool 						Returns true if successful, false otherwise
	 */
	function updateStatus($status_id) {
		return false;
	}

}

class EvaluationStatus extends Model
{
	public static $_table = "EVAL_Statuses";
	public static $_id_column = "status_id";

}
