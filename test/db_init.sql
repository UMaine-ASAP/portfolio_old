-- ---------------------------
-- ---------------------------
-- Globals

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS=0;


-- ---------------------------
-- ---------------------------
-- Tables

-- ---
-- 'REPO_Access_levels'
-- Access levels specified for ownership, editing, reading, etc. rights
-- ---

DROP TABLE IF EXISTS `REPO_Access_levels`;
		
CREATE TABLE `REPO_Access_levels` (
  `access_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`access_id`)
) COMMENT='Access levels specified for ownership, editing, reading, etc';

-- ---
-- 'REPO_Colleges'
-- Designates specific colleges within the University
-- ---

DROP TABLE IF EXISTS `REPO_Colleges`;
		
CREATE TABLE `REPO_Colleges` (
  `college_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `owner_user_id` INTEGER NOT NULL,
  PRIMARY KEY (`college_id`)
) COMMENT='Designates specific colleges within the University';

-- ---
-- 'REPO_Departments'
-- Specific departments within colleges
-- ---

DROP TABLE IF EXISTS `REPO_Departments`;
		
CREATE TABLE `REPO_Departments` (
  `dept_id` INTEGER NOT NULL AUTO_INCREMENT,
  `college_id` INTEGER NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `owner_user_id` INTEGER NOT NULL,
  PRIMARY KEY (`dept_id`)
) COMMENT='Specific departments within colleges';

-- ---
-- 'REPO_Classes'
-- Classes taught within departments
-- ---

DROP TABLE IF EXISTS `REPO_Classes`;
		
CREATE TABLE `REPO_Classes` (
  `class_id` INTEGER NOT NULL AUTO_INCREMENT,
  `dept_id` INTEGER NOT NULL,
  `number` INTEGER NOT NULL DEFAULT 101,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `owner_user_id` INTEGER NOT NULL,
  PRIMARY KEY (`class_id`)
) COMMENT='Classes taught within departments';

-- ---
-- 'REPO_Sections'
-- Class sections (aka class instantiations)
-- ---

DROP TABLE IF EXISTS `REPO_Sections`;
		
CREATE TABLE `REPO_Sections` (
  `section_id` INTEGER NOT NULL AUTO_INCREMENT,
  `class_id` INTEGER NOT NULL,
  `section_number` VARCHAR(255) NOT NULL DEFAULT '0001',
  `day_sched` INTEGER NULL DEFAULT NULL,
  `time` TIME NULL DEFAULT NULL,
  `instructor_user_id` INTEGER NOT NULL,
  `semester` ENUM('Fall', 'Spring', 'Summer') NOT NULL,
  `year` INTEGER NOT NULL,
  `designator` CHAR(3) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`section_id`)
) COMMENT='Class sections (aka class instantiations)';

-- ---
-- 'REPO_Section_access_map'
-- Mapping of access levels to media for users and groups
-- ---

DROP TABLE IF EXISTS `REPO_Section_access_map`;
		
CREATE TABLE `REPO_Section_access_map` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `section_id` INTEGER NOT NULL,
  `group_id` INTEGER NOT NULL,
  `access_type` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Mapping of access levels to sections for users and groups';

-- ---
-- 'REPO_Day_schedules'
-- Possible combinations of days of the week classes are held, mapped to ids
-- ---

DROP TABLE IF EXISTS `REPO_Day_schedules`;
		
CREATE TABLE `REPO_Day_schedules` (
  `sched_id` INTEGER NOT NULL AUTO_INCREMENT,
  `days_of_week` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`sched_id`)
) COMMENT='Possible combinations of days of the week classes are held';

-- ---
-- 'REPO_Assignments'
-- Assignments created for a project to be done for. (provides context for projects)
-- ---

DROP TABLE IF EXISTS `REPO_Assignments`;
		
CREATE TABLE `REPO_Assignments` (
  `assign_id` INTEGER NOT NULL AUTO_INCREMENT,
  `section_id` INTEGER NOT NULL,
  `portfolio_id` INTEGER NULL DEFAULT NULL,
  `creator_user_id` INTEGER NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `requirements` TEXT NULL DEFAULT NULL,
  `due_date` DATE NULL DEFAULT NULL,
  PRIMARY KEY (`assign_id`)
) COMMENT='Assignments created for a project to be done for';

-- ---
-- 'REPO_Assignment_access_map'
-- Mapping of access levels to specific assignments
-- ---

DROP TABLE IF EXISTS `REPO_Assignment_access_map`;
		
CREATE TABLE `REPO_Assignment_access_map` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `assign_id` INTEGER NOT NULL,
  `group_id` INTEGER NOT NULL,
  `access_type` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Mapping of access levels to specific assignments';

-- ---
-- 'REPO_Projects'
-- Unit of work, typically done for an assignment
-- ---

DROP TABLE IF EXISTS `REPO_Projects`;
		
CREATE TABLE `REPO_Projects` (
  `proj_id` INTEGER NOT NULL AUTO_INCREMENT,
  `creator_user_id` INTEGER NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `private` bit(1) NOT NULL DEFAULT 1,
  `type` INTEGER NOT NULL,
  PRIMARY KEY (`proj_id`)
) COMMENT='Unit of work, typically done for an assignment';

-- ---
-- 'REPO_Project_types'
-- Specification of types of media (ex: gallery, article, etc.)
-- ---

DROP TABLE IF EXISTS `REPO_Project_types`;
		
CREATE TABLE `REPO_Project_types` (
  `type_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) COMMENT='Types of media (ex: gallery, article, etc.)';

-- ---
-- 'REPO_Project_access_map'
-- Mapping of access types to projects for users and groups
-- ---

DROP TABLE IF EXISTS `REPO_Project_access_map`;
		
CREATE TABLE `REPO_Project_access_map` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `proj_id` INTEGER NOT NULL,
  `group_id` INTEGER NOT NULL,
  `access_type` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Mapping of access types to projects for users and groups';

-- ---
-- 'REPO_Portfolios'
-- Custom collections of works for organizations and users
-- ---

DROP TABLE IF EXISTS `REPO_Portfolios`;
		
CREATE TABLE `REPO_Portfolios` (
  `port_id` INTEGER NOT NULL AUTO_INCREMENT,
  `owner_user_id` INTEGER NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `private` bit(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`port_id`)
) COMMENT='Custom collections of works for organizations and users';

-- ---
-- 'REPO_Portfolio_access_map'
-- Access mapping of groups to their permissions for specific portfolios.
-- ---

DROP TABLE IF EXISTS `REPO_Portfolio_access_map`;
		
CREATE TABLE `REPO_Portfolio_access_map` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `port_id` INTEGER NOT NULL,
  `group_id` INTEGER NOT NULL,
  `access_type` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Access mapping of groups to their permissions';

-- ---
-- 'REPO_Portfolio_project_map'
-- Collections of bodies of work, and how they relate to other portfolios (must be careful not to have circular portfolios)
-- ---

DROP TABLE IF EXISTS `REPO_Portfolio_project_map`;
		
CREATE TABLE `REPO_Portfolio_project_map` (
  `port_id` INTEGER NOT NULL,
  `child_id` INTEGER NOT NULL,
  `child_is_portfolio` bit(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`port_id`, `child_id`)
) COMMENT='A map of projects and portfolios to portfolios';

-- ---
-- 'AUTH_Users'
-- All registered users of the system
-- ---

DROP TABLE IF EXISTS `AUTH_Users`;
		
CREATE TABLE `AUTH_Users` (
  `user_id` INTEGER NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL UNIQUE,
  `pass` VARCHAR(255) NOT NULL,
  `first` VARCHAR(255) NOT NULL,
  `middle` VARCHAR(255) NULL DEFAULT NULL,
  `last` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NULL DEFAULT NULL,
  `email_priv` bit(1) NOT NULL DEFAULT 0,
  `addn_contact` VARCHAR(255) NULL DEFAULT NULL,
  `bio` TEXT NOT NULL,
  `user_pic` TEXT NULL DEFAULT NULL,
  `major` INTEGER NULL DEFAULT NULL,
  `minor` INTEGER NULL DEFAULT NULL,
  `grad_year` INTEGER NULL DEFAULT NULL,
  `type_id` INTEGER NOT NULL,
  `deactivated` bit(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`)
) COMMENT='All registered users of the system';

-- ---
-- 'AUTH_User_types'
-- Types of users in the system (ex: faculty, admin, staff, undergrad, etc.)
-- ---

DROP TABLE IF EXISTS `AUTH_User_types`;
		
CREATE TABLE `AUTH_User_types` (
  `type_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) COMMENT='Types of users in the system';

-- ---
-- 'AUTH_Groups'
-- Designation and description of groups
-- ---

DROP TABLE IF EXISTS `AUTH_Groups`;
		
CREATE TABLE `AUTH_Groups` (
  `group_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` TEXT NOT NULL,	-- TEXT to accomodate long Portfolio/Proj/etc. titles
  `description` TEXT NULL DEFAULT NULL,
  `owner_user_id` INTEGER NOT NULL,
  `private` bit(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`group_id`)
) COMMENT='Designation and description of groups';

-- ---
-- 'AUTH_Group_user_map'
-- Mapping of people to groups
-- ---

DROP TABLE IF EXISTS `AUTH_Group_user_map`;
		
CREATE TABLE `AUTH_Group_user_map` (
  `group_id` INTEGER NOT NULL,
  `user_id` INTEGER NOT NULL,
  PRIMARY KEY (`group_id`, `user_id`)
) COMMENT='Mapping of people to groups';

-- ---
-- 'REPO_Media'
-- Unit of media contained within a body of work
-- ---

DROP TABLE IF EXISTS `REPO_Media`;
		
CREATE TABLE `REPO_Media` (
  `media_id` INTEGER NOT NULL AUTO_INCREMENT,
  `type` INTEGER NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `created` DATETIME NOT NULL,
  `edited` DATETIME NULL DEFAULT NULL,
  `creator_user_id` INTEGER NOT NULL,
  `filename` TEXT NOT NULL,
  PRIMARY KEY (`media_id`)
) COMMENT='Unit of media contained within a body of work';

-- ---
-- 'REPO_Media_types'
-- Specification of types of media in the system (video, text, picture, pdf, etc.)
-- ---

DROP TABLE IF EXISTS `REPO_Media_types`;
		
CREATE TABLE `REPO_Media_types` (
  `type_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) COMMENT='Types of media in the system (video, text, etc.)';

-- ---
-- 'REPO_Media_access_map'
-- Mapping of access levels to media for users and groups
-- ---

DROP TABLE IF EXISTS `REPO_Media_access_map`;
		
CREATE TABLE `REPO_Media_access_map` (
  `id` INTEGER NOT NULL AUTO_INCREMENT,
  `media_id` INTEGER NOT NULL,
  `group_id` INTEGER NOT NULL,
  `access_type` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
) COMMENT='Mapping of access levels to media for users and groups';

-- ---
-- 'REPO_Project_media_map'
-- Mapping of media to bodies of work
-- ---

DROP TABLE IF EXISTS `REPO_Project_media_map`;
		
CREATE TABLE `REPO_Project_media_map` (
  `proj_id` INTEGER NOT NULL,
  `media_id` INTEGER NOT NULL,
  PRIMARY KEY (`proj_id`, `media_id`)
) COMMENT='Mapping of media to bodies of work';

-- ---
-- 'AUTH_Group_user_role_map'
-- Mapping of group users to their specified role(s)
-- ---

-- DROP TABLE IF EXISTS `AUTH_Group_user_role_map`;
-- 		
-- CREATE TABLE `AUTH_Group_user_role_map` (
--   `group_id` INTEGER NOT NULL,
--   `user_id` INTEGER NOT NULL,
--   `role_id` INTEGER NOT NULL,
--   PRIMARY KEY (`group_id`, `user_id`, `role_id`)
-- ) COMMENT='Mapping of group users to their specified role(s)';

-- ---
-- 'AUTH_Roles'
-- All different roles a user can have within a group (ex: owner, programmer, designer, etc.)
-- ---

-- DROP TABLE IF EXISTS `AUTH_Roles`;
-- 		
-- CREATE TABLE `AUTH_Roles` (
--   `role_id` INTEGER NOT NULL AUTO_INCREMENT,
--   `name` VARCHAR(255) NOT NULL,
--   `description` TEXT NULL DEFAULT NULL,
--   PRIMARY KEY (`role_id`)
-- ) COMMENT='All different roles a user can have within a group';

-- ---
-- 'EVAL_Forms'
-- ---

DROP TABLE IF EXISTS `EVAL_Forms`;
		
CREATE TABLE `EVAL_Forms` (
  `form_id` INTEGER NOT NULL AUTO_INCREMENT,
  `type` INTEGER NULL DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `creator_user_id` INTEGER NOT NULL,
  `private` bit(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`form_id`)
);

-- ---
-- 'EVAL_Components'
-- ---

DROP TABLE IF EXISTS `EVAL_Components`;
		
CREATE TABLE `EVAL_Components` (
  `component_id` INTEGER NOT NULL AUTO_INCREMENT,
  `type` INTEGER NOT NULL,
  `question` TEXT NOT NULL,
  `options` TEXT NULL DEFAULT NULL,
  `required` bit(1) NOT NULL DEFAULT 1,
  `weight` INTEGER NOT NULL,
  `category` INTEGER NOT NULL,
  `private` bit(1) NOT NULL DEFAULT 0,
  `creator_user_id` INTEGER NOT NULL,
  PRIMARY KEY (`component_id`)
);

-- ---
-- 'EVAL_Evaluations'
-- ---

DROP TABLE IF EXISTS `EVAL_Evaluations`;
		
CREATE TABLE `EVAL_Evaluations` (
  `evaluation_id` INTEGER NULL AUTO_INCREMENT DEFAULT NULL,
  `form_id` INTEGER NOT NULL,
  `assigned_by_user_id` INTEGER NOT NULL,
  `created` DATE NOT NULL,
  `due_date` DATE NULL DEFAULT NULL,
  `completed_date` DATE NULL DEFAULT NULL,
  `evaluator_user_id` INTEGER NOT NULL,
  `evaluated_id` INTEGER NOT NULL,
  `status` INTEGER NOT NULL,
  `type` INTEGER NOT NULL,
  PRIMARY KEY (`evaluation_id`)
);

-- ---
-- 'EVAL_Scores'
-- ---

DROP TABLE IF EXISTS `EVAL_Scores`;
		
CREATE TABLE `EVAL_Scores` (
  `component_id` INTEGER NULL DEFAULT NULL,
  `evaluation_id` INTEGER NULL DEFAULT NULL,
  `value` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`component_id`, `evaluation_id`)
);

-- ---
-- Table 'EVAL_Form_component_map'
-- ---

DROP TABLE IF EXISTS `EVAL_Form_component_map`;
		
CREATE TABLE `EVAL_Form_component_map` (
  `form_id` INTEGER NOT NULL,
  `component_id` INTEGER NOT NULL,
  PRIMARY KEY (`form_id`, `component_id`)
);

-- ---
-- 'EVAL_Evaluation_types'
-- Different types of evaluation targets or objects (user, assignment, project, etc.)
-- ---

DROP TABLE IF EXISTS `EVAL_Evaluation_types`;
		
CREATE TABLE `EVAL_Evaluation_types` (
  `type_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) COMMENT='Different types of evaluation targets or objects';

-- ---
-- 'EVAL_Component_types'
-- Specification of different types of components (radio buttons, text, etc.)
-- ---

DROP TABLE IF EXISTS `EVAL_Component_types`;
		
CREATE TABLE `EVAL_Component_types` (
  `type_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) COMMENT='Different types of components (radio buttons, etc.)';

-- ---
-- 'EVAL_Component_categories'
-- Specification of possible categories of components to be grouped together by
-- ---

DROP TABLE IF EXISTS `EVAL_Component_categories`;
		
CREATE TABLE `EVAL_Component_categories` (
  `category_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) COMMENT='Possible categories of components to be grouped together';

-- ---
-- 'EVAL_Statuses'
-- Specification of different statuses of evaluations (ex: partial, complete, pending, archived)
-- ---

DROP TABLE IF EXISTS `EVAL_Statuses`;
		
CREATE TABLE `EVAL_Statuses` (
  `status_id` INTEGER NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`status_id`)
) COMMENT='Specification of different statuses of evaluations';


-- ---------------------------
-- ---------------------------
-- Table Properties

ALTER TABLE `REPO_Colleges` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Departments` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Classes` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Sections` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Section_access_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Day_schedules` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Assignments` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Projects` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `AUTH_Group_user_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Portfolios` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Portfolio_project_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `AUTH_Users` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Project_media_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Media` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `AUTH_Groups` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Portfolio_access_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `AUTH_Group_user_role_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
-- ALTER TABLE `AUTH_Roles` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `AUTH_User_types` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Forms` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Components` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Evaluations` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Scores` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Form_component_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Access_levels` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Evaluation_types` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Component_categories` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Statuses` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `EVAL_Component_types` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Media_types` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Project_types` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Assignment_access_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Project_access_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
ALTER TABLE `REPO_Media_access_map` ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- ---------------------------
-- ---------------------------
-- Foreign Keys
--	NOTE: Must follow the alteration of tables to the InnoDB engine, as foreign keys are not possible
--	in engines other than InnoDB.

ALTER TABLE `REPO_Colleges` ADD FOREIGN KEY (owner_user_id) REFERENCES `AUTH_Groups` (`group_id`);

ALTER TABLE `REPO_Departments` ADD FOREIGN KEY (college_id) REFERENCES `REPO_Colleges` (`college_id`);
ALTER TABLE `REPO_Departments` ADD FOREIGN KEY (owner_user_id) REFERENCES `AUTH_Groups` (`group_id`);

ALTER TABLE `REPO_Classes` ADD FOREIGN KEY (dept_id) REFERENCES `REPO_Departments` (`dept_id`);
ALTER TABLE `REPO_Classes` ADD FOREIGN KEY (owner_user_id) REFERENCES `AUTH_Groups` (`group_id`);

ALTER TABLE `REPO_Sections` ADD FOREIGN KEY (class_id) REFERENCES `REPO_Classes` (`class_id`);
ALTER TABLE `REPO_Sections` ADD FOREIGN KEY (day_sched) REFERENCES `REPO_Day_schedules` (`sched_id`);
ALTER TABLE `REPO_Sections` ADD FOREIGN KEY (instructor_user_id) REFERENCES `AUTH_Users` (`user_id`);

ALTER TABLE `REPO_Section_access_map` ADD FOREIGN KEY (section_id) REFERENCES `REPO_Sections` (`section_id`);
ALTER TABLE `REPO_Section_access_map` ADD FOREIGN KEY (group_id) REFERENCES `AUTH_Groups` (`group_id`);
ALTER TABLE `REPO_Section_access_map` ADD FOREIGN KEY (access_type) REFERENCES `REPO_Access_levels` (`access_id`);

ALTER TABLE `REPO_Assignments` ADD FOREIGN KEY (section_id) REFERENCES `REPO_Sections` (`section_id`);
ALTER TABLE `REPO_Assignments` ADD FOREIGN KEY (creator_user_id) REFERENCES `AUTH_Users` (`user_id`);
ALTER TABLE `REPO_Assignments` ADD FOREIGN KEY (portfolio_id) REFERENCES `REPO_Portfolios` (`port_id`);

ALTER TABLE `REPO_Assignment_access_map` ADD FOREIGN KEY (assign_id) REFERENCES `REPO_Assignments` (`assign_id`);
ALTER TABLE `REPO_Assignment_access_map` ADD FOREIGN KEY (group_id) REFERENCES `AUTH_Groups` (`group_id`);
ALTER TABLE `REPO_Assignment_access_map` ADD FOREIGN KEY (access_type) REFERENCES `REPO_Access_levels` (`access_id`);

ALTER TABLE `REPO_Projects` ADD FOREIGN KEY (creator_user_id) REFERENCES `AUTH_Users` (`user_id`);
ALTER TABLE `REPO_Projects` ADD FOREIGN KEY (type) REFERENCES `REPO_Project_types` (`type_id`);

ALTER TABLE `REPO_Project_media_map` ADD FOREIGN KEY (proj_id) REFERENCES `REPO_Projects` (`proj_id`);
ALTER TABLE `REPO_Project_media_map` ADD FOREIGN KEY (media_id) REFERENCES `REPO_Media` (`media_id`);

ALTER TABLE `REPO_Project_access_map` ADD FOREIGN KEY (proj_id) REFERENCES `REPO_Projects` (`proj_id`);
ALTER TABLE `REPO_Project_access_map` ADD FOREIGN KEY (group_id) REFERENCES `AUTH_Groups` (`group_id`);
ALTER TABLE `REPO_Project_access_map` ADD FOREIGN KEY (access_type) REFERENCES `REPO_Access_levels` (`access_id`);

ALTER TABLE `AUTH_Group_user_map` ADD FOREIGN KEY (group_id) REFERENCES `AUTH_Groups` (`group_id`);
ALTER TABLE `AUTH_Group_user_map` ADD FOREIGN KEY (user_id) REFERENCES `AUTH_Users` (`user_id`);

-- ALTER TABLE `AUTH_Group_user_role_map` ADD FOREIGN KEY (group_id) REFERENCES `AUTH_Group_user_map` (`group_id`);
-- ALTER TABLE `AUTH_Group_user_role_map` ADD FOREIGN KEY (user_id) REFERENCES `AUTH_Group_user_map` (`user_id`);
-- ALTER TABLE `AUTH_Group_user_role_map` ADD FOREIGN KEY (role_id) REFERENCES `AUTH_Roles` (`role_id`);

ALTER TABLE `REPO_Portfolios` ADD FOREIGN KEY (owner_user_id) REFERENCES `AUTH_Users` (`user_id`);

ALTER TABLE `REPO_Portfolio_access_map` ADD FOREIGN KEY (port_id) REFERENCES `REPO_Portfolios` (`port_id`);
ALTER TABLE `REPO_Portfolio_access_map` ADD FOREIGN KEY (group_id) REFERENCES `AUTH_Groups` (`group_id`);
ALTER TABLE `REPO_Portfolio_access_map` ADD FOREIGN KEY (access_type) REFERENCES `REPO_Access_levels` (`access_id`);

-- Left in for documentation purposes: these must be checked in business logic, as we cannot arrange keys like this
-- ALTER TABLE `REPO_Portfolio_project_map` ADD FOREIGN KEY (child_id) REFERENCES `REPO_Projects` (`proj_id`);
-- ALTER TABLE `REPO_Portfolio_project_map` ADD FOREIGN KEY (child_id) REFERENCES `REPO_Portfolios` (`port_id`);
ALTER TABLE `REPO_Portfolio_project_map` ADD FOREIGN KEY (port_id) REFERENCES `REPO_Portfolios` (`port_id`);

ALTER TABLE `AUTH_Users` ADD FOREIGN KEY (type_id) REFERENCES `AUTH_User_types` (`type_id`);

ALTER TABLE `REPO_Media` ADD FOREIGN KEY (type) REFERENCES `REPO_Media_types` (`type_id`);
ALTER TABLE `REPO_Media` ADD FOREIGN KEY (creator_user_id) REFERENCES `AUTH_Users` (`user_id`);

ALTER TABLE `REPO_Media_access_map` ADD FOREIGN KEY (media_id) REFERENCES `REPO_Media` (`media_id`);
ALTER TABLE `REPO_Media_access_map` ADD FOREIGN KEY (group_id) REFERENCES `AUTH_Groups` (`group_id`);
ALTER TABLE `REPO_Media_access_map` ADD FOREIGN KEY (access_type) REFERENCES `REPO_Access_levels` (`access_id`);

ALTER TABLE `EVAL_Forms` ADD FOREIGN KEY (type) REFERENCES `EVAL_Evaluation_types` (`type_id`);
ALTER TABLE `EVAL_Forms` ADD FOREIGN KEY (creator_user_id) REFERENCES `AUTH_Users` (`user_id`);

ALTER TABLE `EVAL_Components` ADD FOREIGN KEY (type) REFERENCES `EVAL_Component_types` (`type_id`);
ALTER TABLE `EVAL_Components` ADD FOREIGN KEY (category) REFERENCES `EVAL_Component_categories` (`category_id`);
ALTER TABLE `EVAL_Components` ADD FOREIGN KEY (creator_user_id) REFERENCES `AUTH_Users` (`user_id`);

ALTER TABLE `EVAL_Evaluations` ADD FOREIGN KEY (form_id) REFERENCES `EVAL_Forms` (`form_id`);
ALTER TABLE `EVAL_Evaluations` ADD FOREIGN KEY (assigned_by_user_id) REFERENCES `AUTH_Users` (`user_id`);
ALTER TABLE `EVAL_Evaluations` ADD FOREIGN KEY (evaluator_user_id) REFERENCES `AUTH_Users` (`user_id`);
ALTER TABLE `EVAL_Evaluations` ADD FOREIGN KEY (status) REFERENCES `EVAL_Statuses` (`status_id`);
ALTER TABLE `EVAL_Evaluations` ADD FOREIGN KEY (type) REFERENCES `EVAL_Evaluation_types` (`type_id`);

ALTER TABLE `EVAL_Scores` ADD FOREIGN KEY (component_id) REFERENCES `EVAL_Components` (`component_id`);
ALTER TABLE `EVAL_Scores` ADD FOREIGN KEY (evaluation_id) REFERENCES `EVAL_Evaluations` (`evaluation_id`);

ALTER TABLE `EVAL_Form_component_map` ADD FOREIGN KEY (form_id) REFERENCES `EVAL_Forms` (`form_id`);
ALTER TABLE `EVAL_Form_component_map` ADD FOREIGN KEY (component_id) REFERENCES `EVAL_Components` (`component_id`);

