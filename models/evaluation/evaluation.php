<?php

/**
 * An Evaluation object represents a single row in the REPO_Evaluations table.
 *
 * @package Models
 */

class Evaluation extends Model
{
	public static $_table = "REPO_Evaluations";
	public static $_id_column = "evaluation_id";


	/**
	 *	Magic-method property getters
	 */
	public function __get($name)
	{
		switch ($name)
		{

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
