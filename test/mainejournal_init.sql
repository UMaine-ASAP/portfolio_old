-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2012 at 11:12 PM
-- Server version: 5.5.21
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mj_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `AUTH_Groups`
--

DROP TABLE IF EXISTS `AUTH_Groups`;
CREATE TABLE IF NOT EXISTS `AUTH_Groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `owner_user_id` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Designation and description of groups' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `AUTH_Groups`
--

INSERT INTO `AUTH_Groups` (`group_id`, `name`, `description`, `owner_user_id`, `private`) VALUES
(1, 'Administrators', 'Sysadmins', 1, 1),
(2, 'New Media 2012 Freshman', 'New Media majors that were freshman in Spring 2012', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `AUTH_Group_user_map`
--

DROP TABLE IF EXISTS `AUTH_Group_user_map`;
CREATE TABLE IF NOT EXISTS `AUTH_Group_user_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of people to groups' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `AUTH_Group_user_map`
--

INSERT INTO `AUTH_Group_user_map` (`id`, `group_id`, `user_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `AUTH_Users`
--

DROP TABLE IF EXISTS `AUTH_Users`;
CREATE TABLE IF NOT EXISTS `AUTH_Users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET latin1 NOT NULL,
  `pass` varchar(255) CHARACTER SET latin1 NOT NULL,
  `first` varchar(255) CHARACTER SET latin1 NOT NULL,
  `middle` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `last` varchar(255) CHARACTER SET latin1 NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `email_priv` tinyint(1) NOT NULL DEFAULT '0',
  `addn_contact` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `bio` text CHARACTER SET latin1,
  `user_pic` text CHARACTER SET latin1,
  `major` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `minor` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `grad_year` int(11) DEFAULT NULL,
  `type_id` int(11) NOT NULL,
  `deactivated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='All registered users of the system' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `AUTH_Users`
--

INSERT INTO `AUTH_Users` (`user_id`, `username`, `pass`, `first`, `middle`, `last`, `email`, `email_priv`, `addn_contact`, `bio`, `user_pic`, `major`, `minor`, `grad_year`, `type_id`, `deactivated`) VALUES
(1, 'admin', 'nothing', 'Admin', NULL, 'Instrator', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `AUTH_User_types`
--

DROP TABLE IF EXISTS `AUTH_User_types`;
CREATE TABLE IF NOT EXISTS `AUTH_User_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Types of users in the system' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `AUTH_User_types`
--

INSERT INTO `AUTH_User_types` (`type_id`, `name`, `description`) VALUES
(0, 'Admin', 'Sysadmin'),
(1, 'Faculty', 'Members of the faculty'),
(2, 'Undergraduates', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Components`
--

DROP TABLE IF EXISTS `EVAL_Components`;
CREATE TABLE IF NOT EXISTS `EVAL_Components` (
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `question` text CHARACTER SET latin1 NOT NULL,
  `options` text CHARACTER SET latin1,
  `required` tinyint(1) NOT NULL DEFAULT '1',
  `weight` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `creator_user_id` int(11) NOT NULL,
  PRIMARY KEY (`component_id`),
  KEY `type` (`type`),
  KEY `category` (`category`),
  KEY `creator_user_id` (`creator_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Component_categories`
--

DROP TABLE IF EXISTS `EVAL_Component_categories`;
CREATE TABLE IF NOT EXISTS `EVAL_Component_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Possible categories of components to be grouped together' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Component_types`
--

DROP TABLE IF EXISTS `EVAL_Component_types`;
CREATE TABLE IF NOT EXISTS `EVAL_Component_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Different types of components (radio buttons, etc.)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Evaluations`
--

DROP TABLE IF EXISTS `EVAL_Evaluations`;
CREATE TABLE IF NOT EXISTS `EVAL_Evaluations` (
  `evaluation_id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `assigned_by_user_id` int(11) NOT NULL,
  `created` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `evaluator_user_id` int(11) NOT NULL,
  `evaluated_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`evaluation_id`),
  KEY `form_id` (`form_id`),
  KEY `assigned_by_user_id` (`assigned_by_user_id`),
  KEY `evaluator_user_id` (`evaluator_user_id`),
  KEY `status` (`status`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Evaluation_types`
--

DROP TABLE IF EXISTS `EVAL_Evaluation_types`;
CREATE TABLE IF NOT EXISTS `EVAL_Evaluation_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Different types of evaluation targets or objects' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Forms`
--

DROP TABLE IF EXISTS `EVAL_Forms`;
CREATE TABLE IF NOT EXISTS `EVAL_Forms` (
  `form_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `creator_user_id` int(11) NOT NULL,
  `private` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`form_id`),
  KEY `type` (`type`),
  KEY `creator_user_id` (`creator_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Form_component_map`
--

DROP TABLE IF EXISTS `EVAL_Form_component_map`;
CREATE TABLE IF NOT EXISTS `EVAL_Form_component_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  KEY `component_id` (`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Scores`
--

DROP TABLE IF EXISTS `EVAL_Scores`;
CREATE TABLE IF NOT EXISTS `EVAL_Scores` (
  `component_id` int(11) NOT NULL DEFAULT '0',
  `evaluation_id` int(11) NOT NULL DEFAULT '0',
  `value` text CHARACTER SET latin1,
  PRIMARY KEY (`component_id`,`evaluation_id`),
  KEY `evaluation_id` (`evaluation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `EVAL_Statuses`
--

DROP TABLE IF EXISTS `EVAL_Statuses`;
CREATE TABLE IF NOT EXISTS `EVAL_Statuses` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Specification of different statuses of evaluations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Access_levels`
--

DROP TABLE IF EXISTS `REPO_Access_levels`;
CREATE TABLE IF NOT EXISTS `REPO_Access_levels` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`access_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Access levels specified for ownership, editing, reading, etc' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `REPO_Access_levels`
--

INSERT INTO `REPO_Access_levels` (`access_id`, `name`, `description`) VALUES
(1, 'OWNER', 'Total ownership of an object'),
(2, 'WRITE', 'May write additional sub-Resources to the resource.'),
(3, 'EDIT', 'May edit existing resources, make no changes to structure (cannot add sub-resources or delete anything)'),
(4, 'READ', 'Can only read the resource, make no changes'),
(5, 'SUBMIT', 'May only submit new resources to a resource, nothing more');

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Assignments`
--

DROP TABLE IF EXISTS `REPO_Assignments`;
CREATE TABLE IF NOT EXISTS `REPO_Assignments` (
  `assign_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) DEFAULT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `requirements` text CHARACTER SET latin1,
  `deactivated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`assign_id`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Assignments instantiated for submissions' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `REPO_Assignments`
--

INSERT INTO `REPO_Assignments` (`assign_id`, `class_id`, `title`, `description`, `requirements`, `deactivated`) VALUES
(1, NULL, 'New Media Freshman Portfolios', 'Year-end summary portfolios for all 1st year students of the New Media program.', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Assignment_access_map`
--

DROP TABLE IF EXISTS `REPO_Assignment_access_map`;
CREATE TABLE IF NOT EXISTS `REPO_Assignment_access_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assign_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `access_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `assign_id` (`assign_id`),
  KEY `group_id` (`group_id`),
  KEY `access_type` (`access_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access levels to assignments' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `REPO_Assignment_access_map`
--

INSERT INTO `REPO_Assignment_access_map` (`id`, `assign_id`, `group_id`, `access_type`) VALUES
(1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Assignment_instances`
--

DROP TABLE IF EXISTS `REPO_Assignment_instances`;
CREATE TABLE IF NOT EXISTS `REPO_Assignment_instances` (
  `instance_id` int(11) NOT NULL AUTO_INCREMENT,
  `assign_id` int(11) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `portfolio_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `requirements` text,
  `due_date` date DEFAULT NULL,
  PRIMARY KEY (`instance_id`),
  KEY `assign_id` (`assign_id`),
  KEY `section_id` (`section_id`),
  KEY `portfolio_id` (`portfolio_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Assignments instantiated for submissions' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `REPO_Assignment_instances`
--

INSERT INTO `REPO_Assignment_instances` (`instance_id`, `assign_id`, `section_id`, `portfolio_id`, `title`, `description`, `requirements`, `due_date`) VALUES
(1, 1, 1, 1, 'New Media Freshman Portfolios 2012', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Assignment_instance_access_map`
--

DROP TABLE IF EXISTS `REPO_Assignment_instance_access_map`;
CREATE TABLE IF NOT EXISTS `REPO_Assignment_instance_access_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `access_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `instance_id` (`instance_id`),
  KEY `group_id` (`group_id`),
  KEY `access_type` (`access_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access levels to assignment instances' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `REPO_Assignment_instance_access_map`
--

INSERT INTO `REPO_Assignment_instance_access_map` (`id`, `instance_id`, `group_id`, `access_type`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Classes`
--

DROP TABLE IF EXISTS `REPO_Classes`;
CREATE TABLE IF NOT EXISTS `REPO_Classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `dept_id` int(11) NOT NULL,
  `number` int(11) NOT NULL DEFAULT '101',
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `owner_user_id` int(11) NOT NULL,
  PRIMARY KEY (`class_id`),
  KEY `dept_id` (`dept_id`),
  KEY `owner_user_id` (`owner_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Classes taught within departments' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `REPO_Classes`
--

INSERT INTO `REPO_Classes` (`class_id`, `dept_id`, `number`, `title`, `description`, `owner_user_id`) VALUES
(1, 1, 0, 'Freshman Portfolios', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Colleges`
--

DROP TABLE IF EXISTS `REPO_Colleges`;
CREATE TABLE IF NOT EXISTS `REPO_Colleges` (
  `college_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `owner_user_id` int(11) NOT NULL,
  PRIMARY KEY (`college_id`),
  KEY `owner_user_id` (`owner_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Designates specific colleges within the University' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `REPO_Colleges`
--

INSERT INTO `REPO_Colleges` (`college_id`, `name`, `description`, `owner_user_id`) VALUES
(1, 'Liberal Arts and Sciences', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Day_schedules`
--

DROP TABLE IF EXISTS `REPO_Day_schedules`;
CREATE TABLE IF NOT EXISTS `REPO_Day_schedules` (
  `sched_id` int(11) NOT NULL AUTO_INCREMENT,
  `days_of_week` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`sched_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Possible combinations of days of the week classes are held' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Departments`
--

DROP TABLE IF EXISTS `REPO_Departments`;
CREATE TABLE IF NOT EXISTS `REPO_Departments` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `college_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `owner_user_id` int(11) NOT NULL,
  PRIMARY KEY (`dept_id`),
  KEY `college_id` (`college_id`),
  KEY `owner_user_id` (`owner_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Specific departments within colleges' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `REPO_Departments`
--

INSERT INTO `REPO_Departments` (`dept_id`, `college_id`, `name`, `description`, `owner_user_id`) VALUES
(1, 1, 'New Media', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Media`
--

DROP TABLE IF EXISTS `REPO_Media`;
CREATE TABLE IF NOT EXISTS `REPO_Media` (
  `media_id` int(11) NOT NULL AUTO_INCREMENT,
  `mimetype` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1 DEFAULT NULL,
  `created` datetime NOT NULL,
  `edited` datetime DEFAULT NULL,
  `filename` text CHARACTER SET latin1 DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  `md5` char(32) CHARACTER SET latin1 DEFAULT NULL,
  `extension` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Unit of media contained within a body of work' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Media_access_map`
--

DROP TABLE IF EXISTS `REPO_Media_access_map`;
CREATE TABLE IF NOT EXISTS `REPO_Media_access_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `access_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`),
  KEY `group_id` (`group_id`),
  KEY `access_type` (`access_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access levels to media for users and groups' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Portfolios`
--

DROP TABLE IF EXISTS `REPO_Portfolios`;
CREATE TABLE IF NOT EXISTS `REPO_Portfolios` (
  `port_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `private` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`port_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Custom collections of works for organizations and users' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `REPO_Portfolios`
--

INSERT INTO `REPO_Portfolios` (`port_id`, `title`, `description`, `private`) VALUES
(1, 'New Media Freshman Portfolio 2012 Submissions', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Portfolio_access_map`
--

DROP TABLE IF EXISTS `REPO_Portfolio_access_map`;
CREATE TABLE IF NOT EXISTS `REPO_Portfolio_access_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `port_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `access_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `port_id` (`port_id`),
  KEY `group_id` (`group_id`),
  KEY `access_type` (`access_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Access mapping of groups to their permissions' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `REPO_Portfolio_access_map`
--

INSERT INTO `REPO_Portfolio_access_map` (`id`, `port_id`, `group_id`, `access_type`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Portfolio_project_map`
--

DROP TABLE IF EXISTS `REPO_Portfolio_project_map`;
CREATE TABLE IF NOT EXISTS `REPO_Portfolio_project_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `port_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `child_is_portfolio` tinyint(1) NOT NULL DEFAULT '0',
  `child_privacy` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `port_id` (`port_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='A map of projects and portfolios to portfolios' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Projects`
--

DROP TABLE IF EXISTS `REPO_Projects`;
CREATE TABLE IF NOT EXISTS `REPO_Projects` (
  `proj_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`proj_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Unit of work, typically done for an assignment' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Project_access_map`
--

DROP TABLE IF EXISTS `REPO_Project_access_map`;
CREATE TABLE IF NOT EXISTS `REPO_Project_access_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proj_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `access_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `proj_id` (`proj_id`),
  KEY `group_id` (`group_id`),
  KEY `access_type` (`access_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access types to projects for users and groups' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Project_media_map`
--

DROP TABLE IF EXISTS `REPO_Project_media_map`;
CREATE TABLE IF NOT EXISTS `REPO_Project_media_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proj_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `proj_id` (`proj_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of media to bodies of work' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Project_types`
--

DROP TABLE IF EXISTS `REPO_Project_types`;
CREATE TABLE IF NOT EXISTS `REPO_Project_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Types of media (ex: gallery, article, etc.)' AUTO_INCREMENT=1 ;

--
-- Dumping data for table `REPO_Project_types`
--

INSERT INTO `REPO_Project_types` (`type_id`, `name`, `description`) VALUES
(1, "Portfolio project", NULL);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Sections`
--

DROP TABLE IF EXISTS `REPO_Sections`;
CREATE TABLE IF NOT EXISTS `REPO_Sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `section_number` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '0001',
  `day_sched` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `instructor_user_id` int(11) NOT NULL,
  `semester` enum('Fall','Spring','Summer') CHARACTER SET latin1 NOT NULL,
  `year` int(11) NOT NULL,
  `designator` char(3) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`section_id`),
  KEY `class_id` (`class_id`),
  KEY `day_sched` (`day_sched`),
  KEY `instructor_user_id` (`instructor_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Class sections (aka class instantiations)' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `REPO_Sections`
--

INSERT INTO `REPO_Sections` (`section_id`, `class_id`, `section_number`, `day_sched`, `time`, `instructor_user_id`, `semester`, `year`, `designator`, `description`) VALUES
(1, 1, '0', NULL, NULL, 1, 'Spring', 2012, 'NMD', 'New Media Freshman Portfolios - Spring 2012');

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Section_access_map`
--

DROP TABLE IF EXISTS `REPO_Section_access_map`;
CREATE TABLE IF NOT EXISTS `REPO_Section_access_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `access_type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `group_id` (`group_id`),
  KEY `access_type` (`access_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access levels to sections for users and groups' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `REPO_Section_access_map`
--

INSERT INTO `REPO_Section_access_map` (`id`, `section_id`, `group_id`, `access_type`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 4);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `AUTH_Group_user_map`
--
ALTER TABLE `AUTH_Group_user_map`
  ADD CONSTRAINT `auth_group_user_map_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `AUTH_Users` (`user_id`),
  ADD CONSTRAINT `auth_group_user_map_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `AUTH_Users`
--
ALTER TABLE `AUTH_Users`
  ADD CONSTRAINT `auth_users_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `AUTH_User_types` (`type_id`);

--
-- Constraints for table `EVAL_Components`
--
ALTER TABLE `EVAL_Components`
  ADD CONSTRAINT `eval_components_ibfk_3` FOREIGN KEY (`creator_user_id`) REFERENCES `AUTH_Users` (`user_id`),
  ADD CONSTRAINT `eval_components_ibfk_1` FOREIGN KEY (`type`) REFERENCES `EVAL_Component_types` (`type_id`),
  ADD CONSTRAINT `eval_components_ibfk_2` FOREIGN KEY (`category`) REFERENCES `EVAL_Component_categories` (`category_id`);

--
-- Constraints for table `EVAL_Evaluations`
--
ALTER TABLE `EVAL_Evaluations`
  ADD CONSTRAINT `eval_evaluations_ibfk_5` FOREIGN KEY (`type`) REFERENCES `EVAL_Evaluation_types` (`type_id`),
  ADD CONSTRAINT `eval_evaluations_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `EVAL_Forms` (`form_id`),
  ADD CONSTRAINT `eval_evaluations_ibfk_2` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `AUTH_Users` (`user_id`),
  ADD CONSTRAINT `eval_evaluations_ibfk_3` FOREIGN KEY (`evaluator_user_id`) REFERENCES `AUTH_Users` (`user_id`),
  ADD CONSTRAINT `eval_evaluations_ibfk_4` FOREIGN KEY (`status`) REFERENCES `EVAL_Statuses` (`status_id`);

--
-- Constraints for table `EVAL_Forms`
--
ALTER TABLE `EVAL_Forms`
  ADD CONSTRAINT `eval_forms_ibfk_2` FOREIGN KEY (`creator_user_id`) REFERENCES `AUTH_Users` (`user_id`),
  ADD CONSTRAINT `eval_forms_ibfk_1` FOREIGN KEY (`type`) REFERENCES `EVAL_Evaluation_types` (`type_id`);

--
-- Constraints for table `EVAL_Form_component_map`
--
ALTER TABLE `EVAL_Form_component_map`
  ADD CONSTRAINT `eval_form_component_map_ibfk_2` FOREIGN KEY (`component_id`) REFERENCES `EVAL_Components` (`component_id`),
  ADD CONSTRAINT `eval_form_component_map_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `EVAL_Forms` (`form_id`);

--
-- Constraints for table `EVAL_Scores`
--
ALTER TABLE `EVAL_Scores`
  ADD CONSTRAINT `eval_scores_ibfk_2` FOREIGN KEY (`evaluation_id`) REFERENCES `EVAL_Evaluations` (`evaluation_id`),
  ADD CONSTRAINT `eval_scores_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `EVAL_Components` (`component_id`);

--
-- Constraints for table `REPO_Assignments`
--
ALTER TABLE `REPO_Assignments`
  ADD CONSTRAINT `repo_assignments_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `REPO_Classes` (`class_id`);

--
-- Constraints for table `REPO_Assignment_access_map`
--
ALTER TABLE `REPO_Assignment_access_map`
  ADD CONSTRAINT `repo_assignment_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`),
  ADD CONSTRAINT `repo_assignment_access_map_ibfk_1` FOREIGN KEY (`assign_id`) REFERENCES `REPO_Assignments` (`assign_id`),
  ADD CONSTRAINT `repo_assignment_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Assignment_instances`
--
ALTER TABLE `REPO_Assignment_instances`
  ADD CONSTRAINT `repo_assignment_instances_ibfk_3` FOREIGN KEY (`portfolio_id`) REFERENCES `REPO_Portfolios` (`port_id`),
  ADD CONSTRAINT `repo_assignment_instances_ibfk_1` FOREIGN KEY (`assign_id`) REFERENCES `REPO_Assignments` (`assign_id`),
  ADD CONSTRAINT `repo_assignment_instances_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `REPO_Sections` (`section_id`);

--
-- Constraints for table `REPO_Assignment_instance_access_map`
--
ALTER TABLE `REPO_Assignment_instance_access_map`
  ADD CONSTRAINT `repo_assignment_instance_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`),
  ADD CONSTRAINT `repo_assignment_instance_access_map_ibfk_1` FOREIGN KEY (`instance_id`) REFERENCES `REPO_Assignment_instances` (`instance_id`),
  ADD CONSTRAINT `repo_assignment_instance_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Classes`
--
ALTER TABLE `REPO_Classes`
  ADD CONSTRAINT `repo_classes_ibfk_2` FOREIGN KEY (`owner_user_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `repo_classes_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `REPO_Departments` (`dept_id`);

--
-- Constraints for table `REPO_Colleges`
--
ALTER TABLE `REPO_Colleges`
  ADD CONSTRAINT `repo_colleges_ibfk_1` FOREIGN KEY (`owner_user_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Departments`
--
ALTER TABLE `REPO_Departments`
  ADD CONSTRAINT `repo_departments_ibfk_2` FOREIGN KEY (`owner_user_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `repo_departments_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `REPO_Colleges` (`college_id`);

--
-- Constraints for table `REPO_Media_access_map`
--
ALTER TABLE `REPO_Media_access_map`
  ADD CONSTRAINT `repo_media_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`),
  ADD CONSTRAINT `repo_media_access_map_ibfk_1` FOREIGN KEY (`media_id`) REFERENCES `REPO_Media` (`media_id`),
  ADD CONSTRAINT `repo_media_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Portfolio_access_map`
--
ALTER TABLE `REPO_Portfolio_access_map`
  ADD CONSTRAINT `repo_portfolio_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`),
  ADD CONSTRAINT `repo_portfolio_access_map_ibfk_1` FOREIGN KEY (`port_id`) REFERENCES `REPO_Portfolios` (`port_id`),
  ADD CONSTRAINT `repo_portfolio_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Portfolio_project_map`
--
ALTER TABLE `REPO_Portfolio_project_map`
  ADD CONSTRAINT `repo_portfolio_project_map_ibfk_1` FOREIGN KEY (`port_id`) REFERENCES `REPO_Portfolios` (`port_id`);

--
-- Constraints for table `REPO_Projects`
--
ALTER TABLE `REPO_Projects`
  ADD CONSTRAINT `repo_projects_ibfk_1` FOREIGN KEY (`type`) REFERENCES `REPO_Project_types` (`type_id`);

--
-- Constraints for table `REPO_Project_access_map`
--
ALTER TABLE `REPO_Project_access_map`
  ADD CONSTRAINT `repo_project_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`),
  ADD CONSTRAINT `repo_project_access_map_ibfk_1` FOREIGN KEY (`proj_id`) REFERENCES `REPO_Projects` (`proj_id`),
  ADD CONSTRAINT `repo_project_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Project_media_map`
--
ALTER TABLE `REPO_Project_media_map`
  ADD CONSTRAINT `repo_project_media_map_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `REPO_Media` (`media_id`),
  ADD CONSTRAINT `repo_project_media_map_ibfk_1` FOREIGN KEY (`proj_id`) REFERENCES `REPO_Projects` (`proj_id`);

--
-- Constraints for table `REPO_Sections`
--
ALTER TABLE `REPO_Sections`
  ADD CONSTRAINT `repo_sections_ibfk_3` FOREIGN KEY (`instructor_user_id`) REFERENCES `AUTH_Users` (`user_id`),
  ADD CONSTRAINT `repo_sections_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `REPO_Classes` (`class_id`),
  ADD CONSTRAINT `repo_sections_ibfk_2` FOREIGN KEY (`day_sched`) REFERENCES `REPO_Day_schedules` (`sched_id`);

--
-- Constraints for table `REPO_Section_access_map`
--
ALTER TABLE `REPO_Section_access_map`
  ADD CONSTRAINT `repo_section_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`),
  ADD CONSTRAINT `repo_section_access_map_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `REPO_Sections` (`section_id`),
  ADD CONSTRAINT `repo_section_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
