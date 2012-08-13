<?php

require_once('libraries/Idiorm/idiorm.php');
require_once('libraries/Paris/paris.php');
require_once('models/section.php');

/**
 * 	Section controller.
 *
 * 	@package Controllers
 */
class SectionController
{
    /**
     *
     * @param type $sectionID
     * @return type 
     */
    public static function getSection($sectionID)
    {
        return Model::factory('Section')->find_one($sectionID);
    }
    
    /**
     *
     * @param type $classID
     * @param type $sectionNumber
     * @param type $daySched
     * @param type $time
     * @param type $instructorID
     * @param type $semester
     * @param type $year
     * @param type $designator
     * @param type $description 
     */
    public function createSection($classID, $sectionNumber, $daySched, $time, $instructorID, $semester, $year, $designator, $description)
    {
        
    }
    
    /**
     *
     * @param type $classID 
     */
    public function addClassToSection($sectionID, $classID)
    {
        //TODO: placeholder
    }
    
    /**
     *
     * @param type $sectionID
     * @param type $classID 
     */
    public function removeClassFromSection($sectionID, $classID)
    {
        //TODO: placeholder
    }
    
    
    public function editSection($sectionID, $sectionNumber, $daySched, $time, $instructorID, $semester, $year, $designator, $description)
    {
        //TODO: placeholder
    }
}

?>
