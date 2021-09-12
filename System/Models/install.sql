--
-- openGuildHall
-- Code for creating the tables, no data insertion
--;

CREATE TABLE `prefix_forum_boards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `parent` int(10) DEFAULT NULL,
  `position` int(10) unsigned NOT NULL,
  `category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `prefix_forum_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `position` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `prefix_forum_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `copy` text NOT NULL,
  `date` int(12) NOT NULL,
  `disable_smileys` smallint(6) NOT NULL,
  `disable_bbcode` smallint(6) NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `as_council` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `prefix_forum_threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `author_id` int(11) unsigned NOT NULL,
  `board_id` int(10) unsigned NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  `original_date` int(10) unsigned NOT NULL,
  `original_post_id` int(10) NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `moved` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `as_council` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `board_id` (`board_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `prefix_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE `prefix_group_perms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL,
  `page` varchar(30) NOT NULL,
  `action` varchar(30) NOT NULL,
  `item` int(10) unsigned DEFAULT NULL,
  `access` varchar(6) NOT NULL DEFAULT 'ALLOW',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `prefix_pages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `body` text NOT NULL,
  `hidden` int(1) unsigned NOT NULL DEFAULT '0',
  `last_updated` int(10) NOT NULL,
  `updated_by` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE `prefix_users` (
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
  `yim` varchar(16) DEFAULT NULL,
  `winLive` varchar(50) DEFAULT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1

