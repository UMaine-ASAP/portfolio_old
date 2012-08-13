<?php

class ClassModel extends Model
{
	public static $_table = 'REPO_Classes';
	public static $_id_column = 'class_id';
        
            
        /**
        * Retrieves a list of assignments belonging to the class
        * @return An array containing Assignment ORM objects, or false if no assignments were found
        */
        public function assignments()
        {
            return Model::factory('Assignment')
                    ->where('class_id', $this->id)
                    ->find_many();

        }
        
        /**
         * Retrieves all instructors who teach this class
         * @return An array containing User ORM objects, or false if no instructors were found for this class
         */
        public function instructors()
        {
            $sections = Model::factory('section')
                    ->where('class_id', $this->id)
                    ->find_many();
            
            $instructors = array();
            
            foreach($sections as $section)
            {
                $instructors[] = Model::factory('User')
                        ->where('user_id', $section->instructor_user_id)
                        ->find_one();
            }
            
            if (count($instructors) == 0)
            {
                return false;
            }
            
            return $instructors;
        }
}

?>
