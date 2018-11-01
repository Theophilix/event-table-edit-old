DROP TABLE IF EXISTS `#__eventtableedit_details`;
CREATE TABLE IF NOT EXISTS `#__eventtableedit_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `access` tinyint(3) NOT NULL DEFAULT '1',
  `checked_out` int(10) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language` char(7) NOT NULL DEFAULT '*',
  `show_filter` tinyint(1) NOT NULL DEFAULT '0',
  `show_first_row` tinyint(1) NOT NULL DEFAULT '0',
  `show_print_view` tinyint(1) NOT NULL DEFAULT '0',
  `show_pagination` tinyint(1) NOT NULL DEFAULT '0',
  `bbcode` tinyint(1) NOT NULL DEFAULT '0',
  `bbcode_img` tinyint(1) NOT NULL DEFAULT '0',
  `pretext` mediumtext NOT NULL,
  `aftertext` mediumtext NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `edit_own_rows` tinyint(1) NOT NULL DEFAULT '0',
  `dateformat` varchar(25) NOT NULL DEFAULT '%d.%m.%Y',
  `timeformat` varchar(25) NOT NULL DEFAULT '%H:%M',
  `cellspacing` tinyint(3) NOT NULL DEFAULT '0',
  `cellpadding` tinyint(3) NOT NULL DEFAULT '2',
  `tablecolor1` varchar(15) NOT NULL DEFAULT 'CCCCCC',
  `tablecolor2` varchar(15) NOT NULL DEFAULT 'FFFFFF',
  `float_separator` char(1) NOT NULL DEFAULT ',',
  `link_target` varchar(15) NOT NULL DEFAULT '_blank',
  `cellbreak` int(11) NOT NULL DEFAULT '0',
  `pagebreak` int(11) NOT NULL DEFAULT '100',
  `asset_id` int(10) NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `normalorappointment` tinyint(1) NOT NULL DEFAULT '0',
  `addtitle` tinyint(1) NOT NULL DEFAULT '0',
  `location` varchar(255) NOT NULL,
  `summary` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `adminemailsubject` varchar(500) NOT NULL,
  `useremailsubject` varchar(500) NOT NULL,
  `useremailtext` text NOT NULL,
  `adminemailtext` text NOT NULL,
  `displayname` varchar(255) NOT NULL,
  `icsfilename` varchar(255) NOT NULL,
  `sorting` tinyint(1) NOT NULL DEFAULT '0',
  `switcher` tinyint(1) NOT NULL DEFAULT '0',
  `row` int(11) NOT NULL,
  `col` int(11) NOT NULL,
  `hours` int(11) NOT NULL,
  `showdayname` tinyint(4) NOT NULL,
  `rowsort` tinyint(4) NOT NULL DEFAULT '0',
  `rowdelete` tinyint(4) NOT NULL DEFAULT '0',
  `showusernametoadmin` tinyint(4) NOT NULL,
  `showusernametouser` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `#__eventtableedit_details`
ADD `automate_sort` tinyint(4) NOT NULL,
ADD `automate_sort_column` varchar(255) NOT NULL AFTER `automate_sort`,
COMMENT='';

DROP TABLE IF EXISTS `#__eventtableedit_heads`;
CREATE TABLE `#__eventtableedit_heads` (
	`id` int(11) NOT NULL auto_increment,
	`table_id` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`datatype` varchar(25) NOT NULL,
	`ordering` int(11) NOT NULL,
	`defaultSorting` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__eventtableedit_dropdowns`;
CREATE TABLE `#__eventtableedit_dropdowns` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar(255) NOT NULL,
	`published` tinyint(1) NOT NULL DEFAULT 1,
	`checked_out` int(10) NOT NULL,
	`checked_out_time` datetime NOT NULL,
	`ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__eventtableedit_dropdown`;
CREATE TABLE `#__eventtableedit_dropdown` (
	`id` int(11) NOT NULL auto_increment,
	`dropdown_id` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
