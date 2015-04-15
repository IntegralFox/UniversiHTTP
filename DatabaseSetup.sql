-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `universihttp` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `universihttp`;

CREATE TABLE `assignment` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `assignment_name` varchar(100) NOT NULL,
  `assignment_description` text,
  `assignment_due` datetime NOT NULL,
  `assignment_points` int(11) DEFAULT NULL COMMENT 'How many points this assignment is worth',
  `course_id` int(11) NOT NULL COMMENT 'Course that this assignment is a part of',
  PRIMARY KEY (`assignment_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `assignment_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `course` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `course_number` varchar(15) NOT NULL,
  `course_title` varchar(100) DEFAULT NULL,
  `course_year` int(11) NOT NULL,
  `course_description` text,
  `term_id` int(11) NOT NULL COMMENT 'Term of course',
  `user_id` int(11) NOT NULL COMMENT 'Instructor of course',
  PRIMARY KEY (`course_id`),
  KEY `term_id` (`term_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `course_ibfk_1` FOREIGN KEY (`term_id`) REFERENCES `term` (`term_id`),
  CONSTRAINT `course_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `course_user_bridge` (
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`course_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `course_user_bridge_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `course_user_bridge_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `file` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `file_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_mime_type` varchar(255) NOT NULL,
  `file_binary` mediumblob NOT NULL COMMENT 'Max size 15 Mb',
  `file_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `folder_id` int(11) DEFAULT NULL COMMENT 'Folder this file is in',
  `assignment_id` int(11) NOT NULL COMMENT 'Assignment this file is a part of',
  `user_id` int(11) NOT NULL COMMENT 'User who owns the file',
  PRIMARY KEY (`file_id`),
  KEY `folder_id` (`folder_id`),
  KEY `assignment_id` (`assignment_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `file_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `folder` (`folder_id`) ON DELETE CASCADE,
  CONSTRAINT `file_ibfk_2` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`assignment_id`) ON DELETE CASCADE,
  CONSTRAINT `file_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `folder` (
  `folder_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `folder_name` varchar(255) NOT NULL,
  `folder_parent_id` int(11) DEFAULT NULL COMMENT 'The folder that this folder is in (or null if in the assignment''s root folder)',
  `assignment_id` int(11) NOT NULL COMMENT 'Assignment this folder is a part of',
  `user_id` int(11) NOT NULL COMMENT 'User who owns this folder',
  PRIMARY KEY (`folder_id`),
  KEY `assignment_id` (`assignment_id`),
  KEY `user_id` (`user_id`),
  KEY `folder_parent_id` (`folder_parent_id`),
  CONSTRAINT `folder_ibfk_3` FOREIGN KEY (`folder_parent_id`) REFERENCES `folder` (`folder_id`) ON DELETE CASCADE,
  CONSTRAINT `folder_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`assignment_id`) ON DELETE CASCADE,
  CONSTRAINT `folder_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `grade` (
  `assignment_id` int(11) NOT NULL COMMENT 'This assignment''s grade',
  `user_id` int(11) NOT NULL COMMENT 'for this user',
  `grade_points` int(11) DEFAULT NULL,
  `grade_comment` text,
  PRIMARY KEY (`assignment_id`,`user_id`),
  KEY `user_id` (`user_id`),
  KEY `assignment_id` (`assignment_id`),
  CONSTRAINT `grade_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignment` (`assignment_id`) ON DELETE CASCADE,
  CONSTRAINT `grade_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `term` (
  `term_id` int(11) NOT NULL AUTO_INCREMENT,
  `term_name` varchar(10) NOT NULL,
  PRIMARY KEY (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `term` (`term_id`, `term_name`) VALUES
(1,	'Fall'),
(2,	'Winter'),
(3,	'Spring'),
(4,	'Summer');

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',
  `user_login` varchar(20) NOT NULL,
  `user_name_last` varchar(30) NOT NULL,
  `user_name_first` varchar(30) NOT NULL,
  `user_name_middle` varchar(30) DEFAULT NULL,
  `user_faculty` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is the user an instructor',
  `user_password` varchar(60) NOT NULL,
  `user_sec_question` varchar(512) DEFAULT NULL,
  `user_sec_answer` varchar(100) DEFAULT NULL,
  `user_temp_password` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`user_id`, `user_login`, `user_name_last`, `user_name_first`, `user_name_middle`, `user_faculty`, `user_password`, `user_sec_question`, `user_sec_answer`, `user_temp_password`) VALUES
(1,	'instructor',	'Smith',	'John',	'Dominic',	1,	'$2y$10$9thCtRNHlhMVtvkYCV5.qutPET8RoQHCIsVmu1NrY8ztfH58Dk6N.',	'Question',	'Answer',	0);

-- 2015-04-15 21:44:34
