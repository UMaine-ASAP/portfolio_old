<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/evaluation/EvaluationAssignment.php');

/**
 * Evaluation Assignment controller.
 *
 * @package Controllers
 */
class EvaluationAssignmentController
{
	/************************************************************************************
	 * Evaluation Assignment object management											*
	 ***********************************************************************************/

	/**
	 *	Creates a new Evaluation Assignment object in the system.
	 *
	 *	The creating User must have the required privileges on the Group to which the Evaluation belongs
	 *	(if a Class is specified).
	 *
	 *	@param	int							$group_id		Identifier of the Group the Evaluation belongs to (optional)
	 *	@param	int							$form_id		Identifier of the form the Evaluation is based on
	 *  @param  int  						$evaluated_id	Identifier of the item/user to be evaluated
	 *  @param 	string 						$type 					
	 *  @param 	date('MM-DD-YYYY')|NULL		$due_date		The due date for this evaluation, null for no due date (optional)
	 *  @param  int 						$evaluator_id 	Identifier of the user to evaluate the item/user being evaluated
	 *
	 *	@return object|bool					The created Evaluation object if successful, false otherwise
 	 */
	public static function assignEvaluations($group_id, $form_id, $evaluated_id, $type, $due_date, $evaluator_id)
	{
		if (//(!$user_id = EvaluationController::get_current_user_id()) ||
			(!$evaluation = Model::factory('Evaluation')->create()))
		{
			return false;
		}
				
		if (!is_null($group_id) && $class = ClassController::getClass($class_id))
		{
			// Check for permissions on Group object here
			// $class->permissions
		}
		$evaluation->created = date();

		//@TODO: finish evaluation code ...

		// Add current User as OWNER
		$evaluation->assigned_by_user_id = $user_id->id();

		if (!$evaluation->save())
		{
			$evaluation->destroy();		// Assume these succeed
			return false;
		}

		return $evaluation;
	}

	/**
	 * Get results of completed evaluations
	 * 
	 * This function aggregates the scores for all completed evaluations tied to this particular assignment.
	 * 
	 * @param int 			$evalAssign_id		The evaluation assignment to return the results for
	 * 
	 * @return array|bool 						Values for evaluation, false if unsuccessful
	 * 
	 * Format of output:
	 * 		[component_id]=>(component, value=>number, label=>label_name,  length=>number_of_scores_found)
	 * 		[component_id]=>(component, value=>(text1, text2, text3, ...), length=>number_of_scores_found)
	 */
	function getResults($evalAssign_id) {
		// @NOTE: this intermediary table between form and evaluations has not been implemented yet so we need to use the form id
		$evaluations = Model::factory('Evaluation')->where('form_id', $evalAssign_id)->find_many();

		$results = array();

		foreach( $evaluations as $evaluation ) {
			$scores = Model::factory('Scores')->where('evaluation_id', $evaluation->evaluation_id)->find_many();

			foreach( $scores as $score ) {
				$component = $score->component;
				$component_id = $component->component_id;
				$component_exists_in_results = (array_search($component->component_id, array_keys($results) ) !== false);

				if( ! $component_exists_in_results ) {
					//New addition to results
					switch( $component->type->name ) {
						case 'radio':
							$value = array($score->value);
							$results[$component_id] = array($component, 'value'=>$value, 'length'=>1);
						break;
						case 'text':
							$options = explode('#', $component->options);
							$label = $options[$score->value-1];
							$value = $score->value;
							$results[$component_id] = array('component'=>$component, 'label'=>$label, 'value'=>$value, 'length'=>1);						
						break;
						default:
						return false;
						break;
					}
				} else {
					//Add value to old values
					$curr_value = $results[$component_id];
					$results[$component_id]['length'] += 1;

					switch( $component->type->name ) {
						case 'radio':
							$results[$component_id]['value'] += $score->value;
						break;
						case 'text':
							$results[$component_id]['value'][] = $score->value;
						break;
						default:
						return false;
						break;
					}

				}

				// Average results for radio buttons
				// @TODO
			}

		}

	}

}

