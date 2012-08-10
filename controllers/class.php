<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/class.php');

/**
 * Class controller.
 *
 * @package Controllers
 */
class ClassController
{
    /**
     * Returns a Class ORM object with the specified ID, or false if none were found
     * @param int $id The ID of the class 
     * @return The class ORM object, or false if no classes with $id exist
     */
    public static function getClass($id)
    {
        return Model::factory('ClassModel')->find_one($id);
    }
    
    /**
     * Retrieves a list of assignments belonging to the class
     * @param int $id The ID of the class 
     * @return An array containing Assignment ORM objects, or false if no assignments were found
     */
    public function getClassAssignments($id)
    {
        if (!$class = getClass($id))
        {
            return false; //there's no such class
        }
        
        $assignments = Model::factory('Assignment')
                ->where('class_id', $class->id)
                ->find_many();
        
        if (count($assignments) == 0)
        {
            return false;
        }
        
        return $assignments;
    }
    
}

?>
