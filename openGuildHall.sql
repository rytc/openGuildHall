-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `r_forum_boards`
--

CREATE TABLE `r_forum_boards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `parent` int(10) DEFAULT NULL,
  `position` int(10) unsigned NOT NULL,
  `category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `r_forum_boards`
--


-- --------------------------------------------------------

--
-- Table structure for table `r_forum_categories`
--

CREATE TABLE `r_forum_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `position` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `r_forum_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `r_forum_posts`
--

CREATE TABLE `r_forum_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `copy` text NOT NULL,
  `date` int(12) NOT NULL,
  `disable_smileys` smallint(6) NOT NULL,
  `disable_bbcode` smallint(6) NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `r_forum_posts`
--


-- --------------------------------------------------------

--
-- Table structure for table `r_forum_threads`
--

CREATE TABLE `r_forum_threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `author_id` int(11) unsigned NOT NULL,
  `board` int(10) unsigned NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `original_date` int(10) unsigned NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `board` (`board`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `r_forum_threads`
--


-- --------------------------------------------------------

--
-- Table structure for table `r_groups`
--

CREATE TABLE `r_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `r_groups`
--

INSERT INTO `r_groups` (`id`, `name`) VALUES
(1, 'Guest');
INSERT INTO `r_groups` (`id`, `name`) VALUES
(2, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `r_group_perms`
--

CREATE TABLE `r_group_perms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `module` varchar(30) NOT NULL,
  `page` varchar(30) NOT NULL,
  `action` varchar(30) NOT NULL,
  `item` int(10) unsigned DEFAULT NULL,
  `access` varchar(6) NOT NULL DEFAULT 'ALLOW',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `r_group_perms`
--


-- --------------------------------------------------------

--
-- Table structure for table `r_pages`
--

CREATE TABLE `r_pages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `body` text NOT NULL,
  `hidden` int(1) unsigned NOT NULL DEFAULT '0',
  `last_updated` int(10) NOT NULL,
  `updated_by` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `r_pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `r_users`
--

CREATE TABLE `r_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(768) NOT NULL,
  `country` varchar(2) NOT NULL DEFAULT '0',
  `gender` varchar(8) NOT NULL DEFAULT 'none',
  `position` varchar(50) DEFAULT NULL,
  `group` int(10) unsigned NOT NULL DEFAULT '1',
  `posts` int(10) unsigned NOT NULL DEFAULT '0',
  `active` int(1) NOT NULL DEFAULT '0',
  `disabled` int(1) NOT NULL DEFAULT '0',
  `actkey` varchar(16) DEFAULT NULL,
  `authkey` varchar(16) DEFAULT NULL,
  `hidden` int(1) NOT NULL DEFAULT '0',
  `contact_privacy` tinyint(4) NOT NULL DEFAULT '0',
  `theme` varchar(20) DEFAULT NULL,
  `dateformat` varchar(15) NOT NULL,
  `timeformat` varchar(15) NOT NULL,
  `aim` varchar(16) DEFAULT NULL,
  `xfire` varchar(25) DEFAULT NULL,
  `steam` varchar(32) DEFAULT NULL,
  `xbl` varchar(15) DEFAULT NULL,
  `twitter` varchar(15) DEFAULT NULL,
  `website` varchar(50) DEFAULT NULL,
  `about` text,
  `signature` text,
  `avatar` varchar(255) NOT NULL DEFAULT 'none',
  `birthday` int(10) unsigned NOT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'UTC',
  `register_date` int(10) unsigned NOT NULL,
  `member_date` int(10) unsigned NOT NULL,
  `last_seen` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `r_users`
--

INSERT INTO `r_users` (`id`, `username`, `email`, `password`, `country`, `gender`, `position`, `group`, `posts`, `active`, `disabled`, `actkey`, `authkey`, `hidden`, `contact_privacy`, `theme`, `dateformat`, `timeformat`, `aim`, `yim`, `winLive`, `xfire`, `steam`, `xbl`, `twitter`, `website`, `about`, `signature`, `avatar`, `birthday`, `timezone`, `register_date`, `member_date`, `last_seen`) VALUES
(1, 'Admin', 'admin@domain.com', '47ec303092f3949794d5300c80572d8adc66e93e7caaa1e64e1efa4de0b45264c295ef92b35f584d9aeed342d3ca0b9033994c4354e61f128e7868ad936a8560f90bc9bc', '0', 'none', NULL, 2, 0, 1, 0, '', NULL, 0, 0, NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'none', 338083200, 'UTC', 1297990454, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `r_user_perms`
--

CREATE TABLE `r_user_perms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `module` varchar(30) NOT NULL,
  `page` varchar(30) NOT NULL,
  `action` varchar(30) NOT NULL,
  `item` int(10) unsigned DEFAULT NULL,
  `access` varchar(6) NOT NULL DEFAULT 'ALLOW',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `r_user_perms`
--

