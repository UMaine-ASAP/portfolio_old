-- USER TYPES
INSERT INTO `AUTH_User_types` (name, description) VALUES 
('admin', 'Administrator'),
('staff', 'University staff'),
('monkey', 'Primates'),
('human', 'Us'),
('mainejournal', 'MaineJournal staff'),
('undergrad', 'Undergraduates'),
('graduate', 'Graduate students'),
('alumni', 'Alumni of UMO'),
('faculty', 'University faculty'),
('janitor', 'University janitorial team'),
('lackey', ''),
('president', 'Presidents of various things...'),
('community', 'Greater University community'),
('asap', 'ASAP Media Services staff');

-- ACCESS LEVELS
INSERT INTO `REPO_Access_levels` (name, description) VALUES 
('owner', 'Owner of the resource, can do anything they want'),
('write', 'Can only write to resource, cannot view'),
('edit', 'Can only edit the existing resource, cannot add sub-Resources'),
('read', 'Can only view the resource, make no changes'),
('submit', 'User can only submit new material to resource for approval');

-- USERS (NO MAJORS/MINORS)
INSERT INTO `AUTH_Users` (username, pass, first, middle, last, email, email_priv, bio, type_id) VALUES 
('fergie', 'password1', 'President', 'Paul', 'Ferguson', 'fergaliciousDef@maine.edu', 0, '', 1),
('asap', 'asap4u', 'ASAP', '', 'Media Services', 'ASAP@maine.edu', 0, '', 1);

-- GROUPS
INSERT INTO `AUTH_Groups` (name, description, owner_user_id, private) VALUES 
('asap', 'ASAP Media Services', 1, 0),
('NMD302 Group 4', 'Developing a touch wall', 1, 1),
('CUGR Showcase', 'Yearly showcase of undergraduate research', 1, 0),
('MAT258 Group 2', 'Studying something related to mathematics', 1, 1);

-- GROUP USER MAP
INSERT INTO `AUTH_Group_user_map` (group_id, user_id) VALUES 
(1, 2),
(2, 2),
(3, 1),
(4, 2);

-- COLLEGES
INSERT INTO `REPO_Colleges` (name, description, owner_user_id) VALUES 
('Liberal Arts & Sciences', 'College of Liberal Arts and Sciences', 1),
('Engineering', 'College of Engineering', 1),
('Business', 'College of Business', 1);

-- DEPARTMENTS
INSERT INTO `REPO_Departments` (college_id, name, description, owner_user_id) VALUES 
(1, 'Computer Science', 'The best department on campus', 1),
(1, 'New Media', 'Meh', 1),
(1, 'Mathematics', '++', 1),
(2, 'Civil Engineering', 'They build bridges and things', 1),
(2, 'Mechanical Engineering', 'Turbines?', 1),
(2, 'Electrical Engineering', 'Benjamin Carlson', 1),
(3, 'Economics', 'Economists', 1),
(3, 'Business Management', 'Needs no description', 1),
(3, 'Finance', 'Kind of like economics', 1);

-- CLASSES
INSERT INTO `REPO_Classes` (dept_id, number, title, description, owner_user_id) VALUES 
(1, 125, 'Intro To Programming', 'Python taught the hard way', 2),
(1, 250, 'Discrete Math', 'Farely.', 1),
(2, 302, 'Interaction Design', 'Building a touch wall', 2),
(2, 104, 'Intro to Graphic Desgn', 'Some kind of intro course', 1),
(3, 258, 'Intro to Differential Equations & Linear Algebra', '', 1),
(5, 101, 'Intro to Mechanical Stuff', 'Turbines!', 1),
(6, 102, 'Electrical Circuitry', 'Resistors, Transistors, Blisters', 1),
(6, 201, 'Embedded Systems', 'C', 1),
(7, 101, 'Intro to Economics', 'Money', 1),
(7, 104, 'Microeconomics', 'Money', 1),
(7, 105, 'Macroeconomics', 'Money', 1),
(8, 101, 'Managing People', '', 1),
(9, 102, 'Finance For The Common Folk', '', 1);

-- DAY SCHEDULES
INSERT INTO `REPO_Day_schedules` (days_of_week) VALUES 
('Monday'),
('Tuesday'),
('Wednesday'),
('Thursday'),
('Friday'),
('Monday, Wednesday, Friday'),
('Tuesday, Thursday'),
('Online');

-- SECTIONS
INSERT INTO `REPO_Sections` (class_id, section_number, day_sched, time, instructor_user_id, semester, year, designator, description) VALUES 
(3, '0001', 4, '6:00 PM', 1, 'Spring', 2012, 'NMD', 'Interaction Design'),
(5, '0001', 8, '', 1, 'Spring', 2012, 'MAT', 'Diff Eqs. & Lin Alg.');

-- SECTION ACCESS_LEVEL MAP
INSERT INTO `REPO_Section_access_map` (section_id, group_id, access_type) VALUES 
(1, 1, 5),
(1, 2, 5),
(2, 1, 5),
(2, 2, 5);

-- PORTFOLIOS
INSERT INTO `REPO_Portfolios` (title, description, private) VALUES 
('NMD 320 Project 1', 'Create tangible to describe future project', 0),
('MAT 258 Homework 5', 'Pages 256-258, #1b, 5c, 6abc', 1),
('NMD Portfolio', 'Portfolio for Spring 2012', 0),
('CUGR 2012', 'Showcase of undergraduate student work', 0);

-- PORTFOLIO ACCESS_LEVEL MAP
INSERT INTO `REPO_Portfolio_access_map` (port_id, group_id, access_type) VALUES 
(1, 1, 1),
(1, 1, 2),
(3, 2, 1);

-- ASSIGNMENTS
INSERT INTO `REPO_Assignments` (class_id, title, description, requirements) VALUES 
(3, 'NMD Project 1', 'Create tangible to describe future project', 'Please limit to 10 pages, submit in one of the following formats: .doc, .pdf, .svg, .png'),
(5, 'MAT Homework 5', 'Pages 256-258, #1b, 5c, 6abc', 'Please scan and submit in .pdf format');

-- ASSIGNMENT INSTANCES
INSERT INTO `REPO_Assignment_instances` (assign_id, section_id, portfolio_id, title, description, requirements, due_date) VALUES 
(1, 1, 1, NULL, NULL, NULL, '2012-01-02'),
(2, 2, 2, NULL, NULL, 'Please scan and submit in .pdf format, OR fax to my office', '2012-02-04');

-- ASSIGNMENT ACCESS_LEVEL MAP
INSERT INTO `REPO_Assignment_access_map` (assign_id, group_id, access_type) VALUES 
(1, 1, 1),
(2, 2, 1);

-- ASSIGNMENT INSTANCE ACCESS_LEVEL MAP
INSERT INTO `REPO_Assignment_instance_access_map` (instance_id, group_id, access_type) VALUES 
(1, 1, 1),
(2, 2, 1);

-- MEDIA TYPES
INSERT INTO `REPO_Media_types` (name, description) VALUES 
('text', 'Plain text, typically the body of an article'),
('picture', 'Picture file'),
('video', 'Video file');

-- MEDIA
INSERT INTO `REPO_Media` (type, title, description, created, edited, filename) VALUES 
(1, 'NMD302 Touch Wall Tangible', 'Tangible describing the development and implementation of a large-scale multi-touch wall display', '2012-2-3', NULL, '/path/to/file.txt'),
(2, 'NMD302 Touch Wall Illustration', 'Illustration of conceptualized touch wall', '2012-2-3', NULL, '/path/to/file.png'),
(1, 'NMD302 Scratch Sensor Tangible', 'Tangible describing the research and development of sensors to detech scrathing as input to applications', '2012-2-3', '2012-2-5', '/short/path/to/crazy/intense/file.txt'),
(2, 'MAT258 Homework 5', 'Submission', '2012-1-1', NULL, '/math/file.png'),
(2, 'MAT258 Homework 5', 'Submission', '2012-2-1', NULL, '/math/file2.svg'),
(3, 'Crazy Video of Research', 'CUGR 2012 Submission', '2012-3-2', NULL, '/cugr/sub1.mpeg');

-- MEDIA ACCESS_LEVEL MAP
INSERT INTO `REPO_Media_access_map` (media_id, group_id, access_type) VALUES 
(1, 2, 1),
(2, 2, 1),
(3, 2, 1);

-- PROJECT TYPES
INSERT INTO `REPO_Project_types` (name, description) VALUES 
('article', 'Article-style project, similar tot hat of a journal or other periodical'),
('gallery', 'Gallery-style display of medias');

-- PROJECTS
INSERT INTO `REPO_Projects` (title, description, type) VALUES 
('NMD302 Touch Wall', 'Creation and study of touch wall', 1),
('NMD302 Scratch Sensor', 'Research and development of scratch sensor', 2),
('MAT258 Homework 5', 'Submission', 1),
('MAT258 Homework 5', 'Submission', 1),
('Windboard', 'A formal study of the cost-effectiveness of wind-powered keyboards', 1);

-- PROJECT ACCESS_LEVEL MAP
INSERT INTO `REPO_Project_access_map` (proj_id, group_id, access_type) VALUES 
(1, 1, 1),
(2, 1, 1),
(3, 2, 1),
(4, 2, 1),
(5, 2, 1);

-- PROJECT MEDIA MAP
INSERT INTO `REPO_Project_media_map` (proj_id, media_id) VALUES 
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 6);

-- PORTFOLIO PROJECT MAP
INSERT INTO `REPO_Portfolio_project_map` (port_id, child_id, child_is_portfolio, child_privacy) VALUES 
(1, 1, 0, 0),
(1, 2, 0, 1),
(2, 3, 0, 1),
(2, 4, 0, 2),
(4, 5, 0, 1);

-- EVALUATION COMPONENT TYPES
INSERT INTO `EVAL_Component_types` (name, description) VALUES 
('radio button', 'Component giving the user the option of a single choice from many'),
('check boxes', 'Component giving the user the option of multiple choices from many'),
('text box', 'Component giving the user the ability to enter a textual answer');

-- EVALUATION COMPONENT CATEGORIES
INSERT INTO `EVAL_Component_categories` (name, description) VALUES 
('Coders', 'Components related to the evaluation of a programmer'),
('Teamwork', 'Components related to evaluating aspects of teamwork as a group');

-- EVALUATION COMPONENTS
INSERT INTO `EVAL_Components` (type, question, options, required, weight, category, private, creator_user_id) VALUES 
(1, 'On a scale of 1-10, how readable was code produced?', '1,2,3,4,5,6,7,8,9,10', 1, 100, 1, 0, 1),
(2, 'Check all that apply', 'Team meetings were worth attending,Valuable critique was given to group members,Some members did all the work,I feel my contribution to the team was valuable', 0, 100, 2, 1, 1),
(3, 'Additional comments', NULL, 0, 0, 2, 0, 1);

-- EVALUATION TYPES
INSERT INTO `EVAL_Evaluation_types` (name, description) VALUES 
('User', 'Used to evaluate a user of the system (student, etc.)'),
('Project', 'Used to evaluate a project in the system');

-- FORMS
INSERT INTO `EVAL_Forms` (type, name, description, creator_user_id, private) VALUES 
(1, 'NMD302 Peer Review', 'Review between students on in-class assignments', 1, 1),
(2, 'NMD302 Instructor Review', 'Review of students work by instructor', 1, 1),
(1, 'General Peer Review', 'Review between students on in-class assignments', 1, 0);

-- FORM COMPONENT MAP
INSERT INTO `EVAL_Form_component_map` (form_id, component_id) VALUES 
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(3, 2),
(3, 3);

-- EVALUATION STATUSES
INSERT INTO `EVAL_Statuses` (name, description) VALUES 
('Assigned', 'Evaluaion has been assigned, but has not been started'),
('In-progress', 'Evaluation has been started, but not finished'),
('Submitted', 'Evaluation has been submitted');

-- EVALUATIONS
INSERT INTO `EVAL_Evaluations` (form_id, assigned_by_user_id, created, due_date, completed_date, evaluator_user_id, evaluated_id, status, type) VALUES 
(1, 1, '2012-2-1', '2012-2-6', NULL, 1, 2, 1, 1),
(3, 1, '2012-1-11', NULL, NULL, 2, 1, 2, 1),
(2, 2, '2012-3-2', '2012-3-14', '2012-3-8', 2, 1, 3, 2);

-- SCORES
INSERT INTO `EVAL_Scores` (evaluation_id, component_id, value) VALUES 
(2, 2, 'Some members did all the work'),
(3, 1, '10');
