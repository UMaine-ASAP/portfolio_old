<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('libraries/constant.php');
require_once('models/class.php');
require_once('controllers/authentication.php');
require_once('controllers/user.php');

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
        error_log("Cannot get class with ID $id");
        return Model::factory('ClassModel')->find_one($id);
    }

    /**
     * Creates a class in the database.
     * @param type $dept_id The department ID of the class
     * @param type $number The class number
     * @param type $title The class title
     * @param type $description The class description
     * @param type $owner_user_id The user ID of the owner of the class
     */
    public function createClass($dept_id, $number, $title, $description, $owner_user_id)
    {
        if (!$class = Model::factory('ClassModel')->create())
        {
            error_log('Error creating ClassModel');
            return false;
        }
        
        
        if (is_null($number) || is_null($title))
        {
            error_log("Class number and title cannot be null");
        }
        
        $class->dept_id = $dept_id;
        $class->number = $number;
        $class->title = $title;
        $class->description = $description;
        $class->owner_user_id = $owner_user_id;
        
        return $class->save();
    }
    
    /**
     * Deletes the class with ID $classID. User calling must have sufficient privileges.
     * @param int $classID The ID of the class to delete
     * @return boolean True if the deletion succeeded, false otherwise
     */
    public function deleteClass($classID)
    {
        if (!$class = self::getClass($classID) || !$userID = AuthenticationController::getCurrentUserID())
        {
            return false;
        }
        
        if (!$user = UserController::getUser($userID))
        {
            return false;
        }
        
        if (!$userID == $class->owner_user_id || !in_array($user, $class->instructors()))
        {
            error_log("User $userID does not have sufficient privileges to delete class $classID");
        }
        
        return $class->delete();
    }

    /**
     * Modifies a class, then updates those changes in the database.
     * 
     * @param type $classID The new ID of the class, or null
     * @param type $dept_id The new department ID of the class, or null
     * @param type $number The new class number, or null
     * @param type $title The new class title, or null
     * @param type $description The new class description, or null
     * @param type $owner_user_id The new class owner_user_id, or null
     * @return boolean True if the edit succeeded, false otherwise
     */
    public function editClass($classID, $dept_id, $number, $title, $description, $owner_user_id)
    {
        if (!$class = self::getClass($classID) || !$userID = AuthenticationController::getCurrentUserID())
        {
            //the class ID was invalid
            return false;
        }
        
        if (!$user = UserController::getUser($userID))
        {
            //not logged in
            return false;
        }
        
        if (!$userID == $class->owner_user_id || !in_array($user, $class->instructors()))
        {
            error_log("User $userID does not have sufficient privileges to edit class $classID");
            return false;
        }
        
        
        if (!is_null($dept_id)) $class->dept_id = $dept_id;
        if (!is_null($number)) $class->number = $number;
        if (!is_null($title)) $class->title = $title;
        if (!is_null($description)) $class->description = $description;
        if (!is_null($owner_user_id)) $class->owner_user_id = $owner_user_id;
     
        return $class->save();
    }
}

?>
