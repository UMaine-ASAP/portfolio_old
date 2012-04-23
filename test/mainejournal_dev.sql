-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2012 at 09:05 PM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Designation and description of groups' AUTO_INCREMENT=15 ;

--
-- Dumping data for table `AUTH_Groups`
--

INSERT INTO `AUTH_Groups` (`group_id`, `name`, `description`, `owner_user_id`, `private`) VALUES
(1, 'asap', 'ASAP Media Services', 1, 0),
(2, 'NMD302 Group 4', 'Developing a touch wall', 1, 1),
(3, 'CUGR Showcase', 'Yearly showcase of undergraduate research', 1, 0),
(4, 'MAT258 Group 2', 'Studying something related to mathematics', 1, 1),
(5, 'New Media Freshman Portfolio 2012 Permissions', 'Permissions for New Media Freshman Portfolio 2012', 3, 1),
(6, 'Test Project! Permissions', 'Permissions for Test Project!', 3, 1),
(7, 'Test project! Permissions', 'Permissions for Test project!', 3, 1),
(8, 'TEST PORT Permissions', 'Permissions for TEST PORT', 2, 1),
(9, 'Test Project! Permissions', 'Permissions for Test Project!', 3, 1),
(10, 'Test Project! Permissions', 'Permissions for Test Project!', 3, 1),
(11, 'Test Permissions', 'Permissions for Test', 3, 1),
(12, 'Test Project! Permissions', 'Permissions for Test Project!', 3, 1),
(13, 'Blergh Permissions', 'Permissions for Blergh', 3, 1),
(14, 'Another Test Project!! Permissions', 'Permissions for Another Test Project!!', 3, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of people to groups' AUTO_INCREMENT=15 ;

--
-- Dumping data for table `AUTH_Group_user_map`
--

INSERT INTO `AUTH_Group_user_map` (`id`, `group_id`, `user_id`) VALUES
(1, 1, 2),
(2, 2, 2),
(3, 3, 1),
(4, 4, 2),
(5, 5, 3),
(6, 6, 3),
(7, 7, 3),
(8, 8, 2),
(9, 9, 3),
(10, 10, 3),
(11, 11, 3),
(12, 12, 3),
(13, 13, 3),
(14, 14, 3);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='All registered users of the system' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `AUTH_Users`
--

INSERT INTO `AUTH_Users` (`user_id`, `username`, `pass`, `first`, `middle`, `last`, `email`, `email_priv`, `addn_contact`, `bio`, `user_pic`, `major`, `minor`, `grad_year`, `type_id`, `deactivated`) VALUES
(1, 'fergie', 'password1', 'President', 'Paul', 'Ferguson', 'fergaliciousDef@maine.edu', 0, NULL, '', NULL, NULL, NULL, NULL, 1, 0),
(2, 'asap', 'asap4u', 'ASAP', '', 'Media Services', 'ASAP@maine.edu', 0, NULL, '', NULL, NULL, NULL, NULL, 1, 0),
(3, 'username', '$2a$08$rGGmsfcA5woRljxeErIEXebvGz3AwJmHkL6LYvq2/54.yLjvEOf.6', 'Anonymous', NULL, 'User', 'anonymous.user@umit.maine.edu', 1, NULL, NULL, NULL, NULL, NULL, NULL, 2, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Types of users in the system' AUTO_INCREMENT=15 ;

--
-- Dumping data for table `AUTH_User_types`
--

INSERT INTO `AUTH_User_types` (`type_id`, `name`, `description`) VALUES
(1, 'admin', 'Administrator'),
(2, 'staff', 'University staff'),
(3, 'monkey', 'Primates'),
(4, 'human', 'Us'),
(5, 'mainejournal', 'MaineJournal staff'),
(6, 'undergrad', 'Undergraduates'),
(7, 'graduate', 'Graduate students'),
(8, 'alumni', 'Alumni of UMO'),
(9, 'faculty', 'University faculty'),
(10, 'janitor', 'University janitorial team'),
(11, 'lackey', ''),
(12, 'president', 'Presidents of various things...'),
(13, 'community', 'Greater University community'),
(14, 'asap', 'ASAP Media Services staff');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `EVAL_Components`
--

INSERT INTO `EVAL_Components` (`component_id`, `type`, `question`, `options`, `required`, `weight`, `category`, `private`, `creator_user_id`) VALUES
(1, 1, 'On a scale of 1-10, how readable was code produced?', '1,2,3,4,5,6,7,8,9,10', 1, 100, 1, 0, 1),
(2, 2, 'Check all that apply', 'Team meetings were worth attending,Valuable critique was given to group members,Some members did all the work,I feel my contribution to the team was valuable', 0, 100, 2, 1, 1),
(3, 3, 'Additional comments', NULL, 0, 0, 2, 0, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Possible categories of components to be grouped together' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `EVAL_Component_categories`
--

INSERT INTO `EVAL_Component_categories` (`category_id`, `name`, `description`) VALUES
(1, 'Coders', 'Components related to the evaluation of a programmer'),
(2, 'Teamwork', 'Components related to evaluating aspects of teamwork as a group');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Different types of components (radio buttons, etc.)' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `EVAL_Component_types`
--

INSERT INTO `EVAL_Component_types` (`type_id`, `name`, `description`) VALUES
(1, 'radio button', 'Component giving the user the option of a single choice from many'),
(2, 'check boxes', 'Component giving the user the option of multiple choices from many'),
(3, 'text box', 'Component giving the user the ability to enter a textual answer');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `EVAL_Evaluations`
--

INSERT INTO `EVAL_Evaluations` (`evaluation_id`, `form_id`, `assigned_by_user_id`, `created`, `due_date`, `completed_date`, `evaluator_user_id`, `evaluated_id`, `status`, `type`) VALUES
(1, 1, 1, '2012-02-01', '2012-02-06', NULL, 1, 2, 1, 1),
(2, 3, 1, '2012-01-11', NULL, NULL, 2, 1, 2, 1),
(3, 2, 2, '2012-03-02', '2012-03-14', '2012-03-08', 2, 1, 3, 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Different types of evaluation targets or objects' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `EVAL_Evaluation_types`
--

INSERT INTO `EVAL_Evaluation_types` (`type_id`, `name`, `description`) VALUES
(1, 'User', 'Used to evaluate a user of the system (student, etc.)'),
(2, 'Project', 'Used to evaluate a project in the system');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `EVAL_Forms`
--

INSERT INTO `EVAL_Forms` (`form_id`, `type`, `name`, `description`, `creator_user_id`, `private`) VALUES
(1, 1, 'NMD302 Peer Review', 'Review between students on in-class assignments', 1, 1),
(2, 2, 'NMD302 Instructor Review', 'Review of students work by instructor', 1, 1),
(3, 1, 'General Peer Review', 'Review between students on in-class assignments', 1, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Dumping data for table `EVAL_Form_component_map`
--

INSERT INTO `EVAL_Form_component_map` (`id`, `form_id`, `component_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 2, 1),
(5, 3, 2),
(6, 3, 3);

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

--
-- Dumping data for table `EVAL_Scores`
--

INSERT INTO `EVAL_Scores` (`component_id`, `evaluation_id`, `value`) VALUES
(1, 3, '10'),
(2, 2, 'Some members did all the work');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Specification of different statuses of evaluations' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `EVAL_Statuses`
--

INSERT INTO `EVAL_Statuses` (`status_id`, `name`, `description`) VALUES
(1, 'Assigned', 'Evaluaion has been assigned, but has not been started'),
(2, 'In-progress', 'Evaluation has been started, but not finished'),
(3, 'Submitted', 'Evaluation has been submitted');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Access levels specified for ownership, editing, reading, etc' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `REPO_Access_levels`
--

INSERT INTO `REPO_Access_levels` (`access_id`, `name`, `description`) VALUES
(1, 'owner', 'Owner of the resource, can do anything they want'),
(2, 'write', 'Can only write to resource, cannot view'),
(3, 'edit', 'Can only edit the existing resource, cannot add sub-Resources'),
(4, 'read', 'Can only view the resource, make no changes'),
(5, 'submit', 'User can only submit new material to resource for approval');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Assignments instantiated for submissions' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `REPO_Assignments`
--

INSERT INTO `REPO_Assignments` (`assign_id`, `class_id`, `title`, `description`, `requirements`, `deactivated`) VALUES
(1, 3, 'NMD Project 1', 'Create tangible to describe future project', 'Please limit to 10 pages, submit in one of the following formats: .doc, .pdf, .svg, .png', 0),
(2, 5, 'MAT Homework 5', 'Pages 256-258, #1b, 5c, 6abc', 'Please scan and submit in .pdf format', 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access levels to assignments' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `REPO_Assignment_access_map`
--

INSERT INTO `REPO_Assignment_access_map` (`id`, `assign_id`, `group_id`, `access_type`) VALUES
(1, 1, 1, 1),
(2, 2, 2, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Assignments instantiated for submissions' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `REPO_Assignment_instances`
--

INSERT INTO `REPO_Assignment_instances` (`instance_id`, `assign_id`, `section_id`, `portfolio_id`, `title`, `description`, `requirements`, `due_date`) VALUES
(1, 1, 1, 1, NULL, NULL, NULL, '2012-01-02'),
(2, 2, 2, 2, NULL, NULL, 'Please scan and submit in .pdf format, OR fax to my office', '2012-02-04');

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
(2, 2, 2, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Classes taught within departments' AUTO_INCREMENT=14 ;

--
-- Dumping data for table `REPO_Classes`
--

INSERT INTO `REPO_Classes` (`class_id`, `dept_id`, `number`, `title`, `description`, `owner_user_id`) VALUES
(1, 1, 125, 'Intro To Programming', 'Python taught the hard way', 2),
(2, 1, 250, 'Discrete Math', 'Farely.', 1),
(3, 2, 302, 'Interaction Design', 'Building a touch wall', 2),
(4, 2, 104, 'Intro to Graphic Desgn', 'Some kind of intro course', 1),
(5, 3, 258, 'Intro to Differential Equations & Linear Algebra', '', 1),
(6, 5, 101, 'Intro to Mechanical Stuff', 'Turbines!', 1),
(7, 6, 102, 'Electrical Circuitry', 'Resistors, Transistors, Blisters', 1),
(8, 6, 201, 'Embedded Systems', 'C', 1),
(9, 7, 101, 'Intro to Economics', 'Money', 1),
(10, 7, 104, 'Microeconomics', 'Money', 1),
(11, 7, 105, 'Macroeconomics', 'Money', 1),
(12, 8, 101, 'Managing People', '', 1),
(13, 9, 102, 'Finance For The Common Folk', '', 1);

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
(1, 'Liberal Arts & Sciences', 'College of Liberal Arts and Sciences', 1),
(2, 'Engineering', 'College of Engineering', 1),
(3, 'Business', 'College of Business', 1);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Day_schedules`
--

DROP TABLE IF EXISTS `REPO_Day_schedules`;
CREATE TABLE IF NOT EXISTS `REPO_Day_schedules` (
  `sched_id` int(11) NOT NULL AUTO_INCREMENT,
  `days_of_week` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`sched_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Possible combinations of days of the week classes are held' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `REPO_Day_schedules`
--

INSERT INTO `REPO_Day_schedules` (`sched_id`, `days_of_week`) VALUES
(1, 'Monday'),
(2, 'Tuesday'),
(3, 'Wednesday'),
(4, 'Thursday'),
(5, 'Friday'),
(6, 'Monday, Wednesday, Friday'),
(7, 'Tuesday, Thursday'),
(8, 'Online');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Specific departments within colleges' AUTO_INCREMENT=10 ;

--
-- Dumping data for table `REPO_Departments`
--

INSERT INTO `REPO_Departments` (`dept_id`, `college_id`, `name`, `description`, `owner_user_id`) VALUES
(1, 1, 'Computer Science', 'The best department on campus', 1),
(2, 1, 'New Media', 'Meh', 1),
(3, 1, 'Mathematics', '++', 1),
(4, 2, 'Civil Engineering', 'They build bridges and things', 1),
(5, 2, 'Mechanical Engineering', 'Turbines?', 1),
(6, 2, 'Electrical Engineering', 'Benjamin Carlson', 1),
(7, 3, 'Economics', 'Economists', 1),
(8, 3, 'Business Management', 'Needs no description', 1),
(9, 3, 'Finance', 'Kind of like economics', 1);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Media`
--

DROP TABLE IF EXISTS `REPO_Media`;
CREATE TABLE IF NOT EXISTS `REPO_Media` (
  `media_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `created` datetime NOT NULL,
  `edited` datetime DEFAULT NULL,
  `filename` text CHARACTER SET latin1 NOT NULL,
  `filesize` int(11) NOT NULL,
  `md5` char(32) CHARACTER SET latin1 NOT NULL,
  `extension` varchar(10) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`media_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Unit of media contained within a body of work' AUTO_INCREMENT=10 ;

--
-- Dumping data for table `REPO_Media`
--

INSERT INTO `REPO_Media` (`media_id`, `type`, `title`, `description`, `created`, `edited`, `filename`, `filesize`, `md5`, `extension`) VALUES
(1, 1, 'NMD302 Touch Wall Tangible', 'Tangible describing the development and implementation of a large-scale multi-touch wall display', '2012-02-03 00:00:00', NULL, '/path/to/file', 23894, '79054025255fb1a26e4bc422aef54eb4', 'txt'),
(2, 2, 'NMD302 Touch Wall Illustration', 'Illustration of conceptualized touch wall', '2012-02-03 00:00:00', NULL, '/path/to/file', 32652, '79054025255fb1a26e4bc422aef54eb4', 'png'),
(3, 1, 'NMD302 Scratch Sensor Tangible', 'Tangible describing the research and development of sensors to detech scrathing as input to applications', '2012-02-03 00:00:00', '2012-02-05 00:00:00', '/short/path/to/crazy/intense/file', 3243, '79054025255fb1a26e4bc422aef54eb4', 'txt'),
(4, 2, 'MAT258 Homework 5', 'Submission', '2012-01-01 00:00:00', NULL, '/math/file', 32479, '79054025255fb1a26e4bc422aef54eb4', 'png'),
(5, 2, 'MAT258 Homework 5', 'Submission', '2012-02-01 00:00:00', NULL, '/math/file2', 73864, '79054025255fb1a26e4bc422aef54eb4', 'svg'),
(6, 3, 'Crazy Video of Research', 'CUGR 2012 Submission', '2012-03-02 00:00:00', NULL, '/cugr/sub1', 23472, '79054025255fb1a26e4bc422aef54eb4', 'mpeg'),
(7, 2, 'Ballmer tongue', NULL, '2012-04-22 21:53:55', NULL, 'test/pics/ballmer1', 35713, '43dd31724c7d816b733f5d1f771284b2', 'jpg'),
(8, 2, 'Ballmer fingers', NULL, '2012-04-22 16:49:38', NULL, 'test/pics/ballmer2', 53079, 'a0561b20ea970246e4feefb0f549bc17', 'jpg'),
(9, 2, 'Ballmer thumb', NULL, '2012-04-25 17:39:43', NULL, 'test/pics/ballmer3', 76653, 'a4cf1c256ebc82ef08f499020c1253a2', 'jpg');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access levels to media for users and groups' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `REPO_Media_access_map`
--

INSERT INTO `REPO_Media_access_map` (`id`, `media_id`, `group_id`, `access_type`) VALUES
(1, 1, 2, 1),
(2, 2, 2, 1),
(3, 3, 2, 1),
(4, 7, 12, 1),
(5, 8, 12, 1),
(6, 9, 12, 1);

-- --------------------------------------------------------

--
-- Table structure for table `REPO_Media_types`
--

DROP TABLE IF EXISTS `REPO_Media_types`;
CREATE TABLE IF NOT EXISTS `REPO_Media_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Types of media in the system (video, text, etc.)' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `REPO_Media_types`
--

INSERT INTO `REPO_Media_types` (`type_id`, `name`, `description`) VALUES
(1, 'text', 'Plain text, typically the body of an article'),
(2, 'picture', 'Picture file'),
(3, 'video', 'Video file');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Custom collections of works for organizations and users' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `REPO_Portfolios`
--

INSERT INTO `REPO_Portfolios` (`port_id`, `title`, `description`, `private`) VALUES
(1, 'NMD 320 Project 1', 'Create tangible to describe future project', 0),
(2, 'MAT 258 Homework 5', 'Pages 256-258, #1b, 5c, 6abc', 1),
(3, 'NMD Portfolio', 'Portfolio for Spring 2012', 0),
(4, 'CUGR 2012', 'Showcase of undergraduate student work', 0),
(5, 'New Media Freshman Portfolio 2012', 'New Media Freshman Portfolio 2012', 1),
(6, 'TEST PORT', 'TEST DESC', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Access mapping of groups to their permissions' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `REPO_Portfolio_access_map`
--

INSERT INTO `REPO_Portfolio_access_map` (`id`, `port_id`, `group_id`, `access_type`) VALUES
(1, 1, 1, 1),
(2, 1, 1, 2),
(3, 3, 2, 1),
(4, 5, 5, 1),
(5, 6, 8, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='A map of projects and portfolios to portfolios' AUTO_INCREMENT=12 ;

--
-- Dumping data for table `REPO_Portfolio_project_map`
--

INSERT INTO `REPO_Portfolio_project_map` (`id`, `port_id`, `child_id`, `child_is_portfolio`, `child_privacy`) VALUES
(1, 1, 1, 0, 0),
(2, 1, 2, 0, 1),
(3, 2, 3, 0, 1),
(4, 2, 4, 0, 2),
(5, 4, 5, 0, 1),
(9, 5, 9, 0, 0),
(11, 5, 11, 0, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Unit of work, typically done for an assignment' AUTO_INCREMENT=12 ;

--
-- Dumping data for table `REPO_Projects`
--

INSERT INTO `REPO_Projects` (`proj_id`, `title`, `description`, `type`) VALUES
(1, 'NMD302 Touch Wall', 'Creation and study of touch wall', 1),
(2, 'NMD302 Scratch Sensor', 'Research and development of scratch sensor', 2),
(3, 'MAT258 Homework 5', 'Submission', 1),
(4, 'MAT258 Homework 5', 'Submission', 1),
(5, 'Windboard', 'A formal study of the cost-effectiveness of wind-powered keyboards', 1),
(9, 'Test Project!', 'Test project, gnarly!!', 1),
(11, 'Another Test Project!!', 'Sweet!', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access types to projects for users and groups' AUTO_INCREMENT=12 ;

--
-- Dumping data for table `REPO_Project_access_map`
--

INSERT INTO `REPO_Project_access_map` (`id`, `proj_id`, `group_id`, `access_type`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 3, 2, 1),
(4, 4, 2, 1),
(5, 5, 2, 1),
(9, 9, 12, 1),
(11, 11, 14, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of media to bodies of work' AUTO_INCREMENT=10 ;

--
-- Dumping data for table `REPO_Project_media_map`
--

INSERT INTO `REPO_Project_media_map` (`id`, `proj_id`, `media_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 3),
(4, 3, 4),
(5, 4, 5),
(6, 5, 6),
(7, 9, 7),
(8, 9, 8),
(9, 9, 9);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Types of media (ex: gallery, article, etc.)' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `REPO_Project_types`
--

INSERT INTO `REPO_Project_types` (`type_id`, `name`, `description`) VALUES
(1, 'article', 'Article-style project, similar tot hat of a journal or other periodical'),
(2, 'gallery', 'Gallery-style display of medias');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Class sections (aka class instantiations)' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `REPO_Sections`
--

INSERT INTO `REPO_Sections` (`section_id`, `class_id`, `section_number`, `day_sched`, `time`, `instructor_user_id`, `semester`, `year`, `designator`, `description`) VALUES
(1, 3, '0001', 4, '06:00:00', 1, 'Spring', 2012, 'NMD', 'Interaction Design'),
(2, 5, '0001', 8, '00:00:00', 1, 'Spring', 2012, 'MAT', 'Diff Eqs. & Lin Alg.');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Mapping of access levels to sections for users and groups' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `REPO_Section_access_map`
--

INSERT INTO `REPO_Section_access_map` (`id`, `section_id`, `group_id`, `access_type`) VALUES
(1, 1, 1, 5),
(2, 1, 2, 5),
(3, 2, 1, 5),
(4, 2, 2, 5);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `AUTH_Group_user_map`
--
ALTER TABLE `AUTH_Group_user_map`
  ADD CONSTRAINT `auth_group_user_map_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `auth_group_user_map_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `AUTH_Users` (`user_id`);

--
-- Constraints for table `AUTH_Users`
--
ALTER TABLE `AUTH_Users`
  ADD CONSTRAINT `auth_users_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `AUTH_User_types` (`type_id`);

--
-- Constraints for table `EVAL_Components`
--
ALTER TABLE `EVAL_Components`
  ADD CONSTRAINT `eval_components_ibfk_1` FOREIGN KEY (`type`) REFERENCES `EVAL_Component_types` (`type_id`),
  ADD CONSTRAINT `eval_components_ibfk_2` FOREIGN KEY (`category`) REFERENCES `EVAL_Component_categories` (`category_id`),
  ADD CONSTRAINT `eval_components_ibfk_3` FOREIGN KEY (`creator_user_id`) REFERENCES `AUTH_Users` (`user_id`);

--
-- Constraints for table `EVAL_Evaluations`
--
ALTER TABLE `EVAL_Evaluations`
  ADD CONSTRAINT `eval_evaluations_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `EVAL_Forms` (`form_id`),
  ADD CONSTRAINT `eval_evaluations_ibfk_2` FOREIGN KEY (`assigned_by_user_id`) REFERENCES `AUTH_Users` (`user_id`),
  ADD CONSTRAINT `eval_evaluations_ibfk_3` FOREIGN KEY (`evaluator_user_id`) REFERENCES `AUTH_Users` (`user_id`),
  ADD CONSTRAINT `eval_evaluations_ibfk_4` FOREIGN KEY (`status`) REFERENCES `EVAL_Statuses` (`status_id`),
  ADD CONSTRAINT `eval_evaluations_ibfk_5` FOREIGN KEY (`type`) REFERENCES `EVAL_Evaluation_types` (`type_id`);

--
-- Constraints for table `EVAL_Forms`
--
ALTER TABLE `EVAL_Forms`
  ADD CONSTRAINT `eval_forms_ibfk_1` FOREIGN KEY (`type`) REFERENCES `EVAL_Evaluation_types` (`type_id`),
  ADD CONSTRAINT `eval_forms_ibfk_2` FOREIGN KEY (`creator_user_id`) REFERENCES `AUTH_Users` (`user_id`);

--
-- Constraints for table `EVAL_Form_component_map`
--
ALTER TABLE `EVAL_Form_component_map`
  ADD CONSTRAINT `eval_form_component_map_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `EVAL_Forms` (`form_id`),
  ADD CONSTRAINT `eval_form_component_map_ibfk_2` FOREIGN KEY (`component_id`) REFERENCES `EVAL_Components` (`component_id`);

--
-- Constraints for table `EVAL_Scores`
--
ALTER TABLE `EVAL_Scores`
  ADD CONSTRAINT `eval_scores_ibfk_1` FOREIGN KEY (`component_id`) REFERENCES `EVAL_Components` (`component_id`),
  ADD CONSTRAINT `eval_scores_ibfk_2` FOREIGN KEY (`evaluation_id`) REFERENCES `EVAL_Evaluations` (`evaluation_id`);

--
-- Constraints for table `REPO_Assignments`
--
ALTER TABLE `REPO_Assignments`
  ADD CONSTRAINT `repo_assignments_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `REPO_Classes` (`class_id`);

--
-- Constraints for table `REPO_Assignment_access_map`
--
ALTER TABLE `REPO_Assignment_access_map`
  ADD CONSTRAINT `repo_assignment_access_map_ibfk_1` FOREIGN KEY (`assign_id`) REFERENCES `REPO_Assignments` (`assign_id`),
  ADD CONSTRAINT `repo_assignment_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `repo_assignment_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`);

--
-- Constraints for table `REPO_Assignment_instances`
--
ALTER TABLE `REPO_Assignment_instances`
  ADD CONSTRAINT `repo_assignment_instances_ibfk_1` FOREIGN KEY (`assign_id`) REFERENCES `REPO_Assignments` (`assign_id`),
  ADD CONSTRAINT `repo_assignment_instances_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `REPO_Sections` (`section_id`),
  ADD CONSTRAINT `repo_assignment_instances_ibfk_3` FOREIGN KEY (`portfolio_id`) REFERENCES `REPO_Portfolios` (`port_id`);

--
-- Constraints for table `REPO_Assignment_instance_access_map`
--
ALTER TABLE `REPO_Assignment_instance_access_map`
  ADD CONSTRAINT `repo_assignment_instance_access_map_ibfk_1` FOREIGN KEY (`instance_id`) REFERENCES `REPO_Assignment_instances` (`instance_id`),
  ADD CONSTRAINT `repo_assignment_instance_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `repo_assignment_instance_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`);

--
-- Constraints for table `REPO_Classes`
--
ALTER TABLE `REPO_Classes`
  ADD CONSTRAINT `repo_classes_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `REPO_Departments` (`dept_id`),
  ADD CONSTRAINT `repo_classes_ibfk_2` FOREIGN KEY (`owner_user_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Colleges`
--
ALTER TABLE `REPO_Colleges`
  ADD CONSTRAINT `repo_colleges_ibfk_1` FOREIGN KEY (`owner_user_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Departments`
--
ALTER TABLE `REPO_Departments`
  ADD CONSTRAINT `repo_departments_ibfk_1` FOREIGN KEY (`college_id`) REFERENCES `REPO_Colleges` (`college_id`),
  ADD CONSTRAINT `repo_departments_ibfk_2` FOREIGN KEY (`owner_user_id`) REFERENCES `AUTH_Groups` (`group_id`);

--
-- Constraints for table `REPO_Media`
--
ALTER TABLE `REPO_Media`
  ADD CONSTRAINT `repo_media_ibfk_1` FOREIGN KEY (`type`) REFERENCES `REPO_Media_types` (`type_id`);

--
-- Constraints for table `REPO_Media_access_map`
--
ALTER TABLE `REPO_Media_access_map`
  ADD CONSTRAINT `repo_media_access_map_ibfk_1` FOREIGN KEY (`media_id`) REFERENCES `REPO_Media` (`media_id`),
  ADD CONSTRAINT `repo_media_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `repo_media_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`);

--
-- Constraints for table `REPO_Portfolio_access_map`
--
ALTER TABLE `REPO_Portfolio_access_map`
  ADD CONSTRAINT `repo_portfolio_access_map_ibfk_1` FOREIGN KEY (`port_id`) REFERENCES `REPO_Portfolios` (`port_id`),
  ADD CONSTRAINT `repo_portfolio_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `repo_portfolio_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`);

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
  ADD CONSTRAINT `repo_project_access_map_ibfk_1` FOREIGN KEY (`proj_id`) REFERENCES `REPO_Projects` (`proj_id`),
  ADD CONSTRAINT `repo_project_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `repo_project_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`);

--
-- Constraints for table `REPO_Project_media_map`
--
ALTER TABLE `REPO_Project_media_map`
  ADD CONSTRAINT `repo_project_media_map_ibfk_1` FOREIGN KEY (`proj_id`) REFERENCES `REPO_Projects` (`proj_id`),
  ADD CONSTRAINT `repo_project_media_map_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `REPO_Media` (`media_id`);

--
-- Constraints for table `REPO_Sections`
--
ALTER TABLE `REPO_Sections`
  ADD CONSTRAINT `repo_sections_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `REPO_Classes` (`class_id`),
  ADD CONSTRAINT `repo_sections_ibfk_2` FOREIGN KEY (`day_sched`) REFERENCES `REPO_Day_schedules` (`sched_id`),
  ADD CONSTRAINT `repo_sections_ibfk_3` FOREIGN KEY (`instructor_user_id`) REFERENCES `AUTH_Users` (`user_id`);

--
-- Constraints for table `REPO_Section_access_map`
--
ALTER TABLE `REPO_Section_access_map`
  ADD CONSTRAINT `repo_section_access_map_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `REPO_Sections` (`section_id`),
  ADD CONSTRAINT `repo_section_access_map_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `AUTH_Groups` (`group_id`),
  ADD CONSTRAINT `repo_section_access_map_ibfk_3` FOREIGN KEY (`access_type`) REFERENCES `REPO_Access_levels` (`access_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
