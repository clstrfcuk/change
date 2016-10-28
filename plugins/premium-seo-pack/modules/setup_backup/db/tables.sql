-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_link_builder`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_link_builder` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hits` int(10) DEFAULT '0',
  `phrase` varchar(100) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `rel` enum('no','alternate','author','bookmark','help','license','next','nofollow','noreferrer','prefetch','prev','search','tag') DEFAULT 'no',
  `title` varchar(100) DEFAULT NULL,
  `target` enum('no','_blank','_parent','_self','_top') DEFAULT 'no',
  `post_id` int(10) DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `publish` char(1) DEFAULT 'Y',
  `max_replacements` smallint(2) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`phrase`,`url`),
  KEY `url` (`url`),
  KEY `publish` (`publish`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_link_redirect`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_link_redirect` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hits` int(10) DEFAULT '0',
  `url` varchar(150) DEFAULT NULL,
  `url_redirect` varchar(150) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_monitor_404`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_monitor_404` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hits` int(10) DEFAULT '1',
  `url` varchar(200) DEFAULT NULL,
  `referrers` text,
  `user_agents` text,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_web_directories`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_web_directories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `directory_name` varchar(255) DEFAULT NULL,
  `submit_url` varchar(255) DEFAULT NULL,
  `pagerank` double DEFAULT NULL,
  `alexa` double DEFAULT NULL,
  `status` smallint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `submit_url` (`submit_url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_serp_reporter`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_serp_reporter` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`focus_keyword` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`url` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`search_engine` VARCHAR(30) NULL DEFAULT 'google.com' COLLATE 'utf8_unicode_ci',
	`position` INT(10) NULL DEFAULT '999',
	`position_prev` INT(10) NULL DEFAULT '999',
	`position_worst` INT(10) NULL DEFAULT '999',
	`position_best` INT(10) NULL DEFAULT '999',
	`post_id` INT(10) NULL DEFAULT '0',
	`visits` INT(10) NULL DEFAULT '0',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`publish` char(1) DEFAULT 'Y',
	`last_check_status` VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`last_check_msg` TEXT NULL COLLATE 'utf8_unicode_ci',
	`last_check_data` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `unique` (`focus_keyword`, `url`, `search_engine`),
	KEY `search_engine` (`search_engine`),
	KEY `url` (`url`),
	KEY `position` (`position`),
	KEY `position_prev` (`position_prev`),
	KEY `post_id` (`post_id`),
	KEY `publish` (`publish`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_serp_reporter2rank`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_serp_reporter2rank` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`report_id` INT(10) NULL DEFAULT '0',
	`report_day` DATE NULL DEFAULT NULL,
	`position` INT(10) NULL DEFAULT '0',
	`top100` TEXT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `unique` (`report_id`, `report_day`),
	KEY `report_day` (`report_day`),
	KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `{wp_prefix}psp_post_planner_cron`
--

CREATE TABLE IF NOT EXISTS `{wp_prefix}psp_post_planner_cron` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_post` BIGINT(20) NOT NULL,
	`post_to` TEXT NULL,
	`post_to-page_group` VARCHAR(255) NULL DEFAULT NULL,
	`post_privacy` VARCHAR(255) NULL DEFAULT NULL,
	`email_at_post` ENUM('off','on') NOT NULL DEFAULT 'off',
	`status` SMALLINT(1) NOT NULL DEFAULT '0',
	`response` TEXT NULL,
	`started_at` TIMESTAMP NULL DEFAULT NULL,
	`ended_at` TIMESTAMP NULL DEFAULT NULL,
	`run_date` DATETIME NULL DEFAULT NULL,
	`repeat_status` ENUM('off','on') NOT NULL DEFAULT 'off' COMMENT 'one-time | repeating',
	`repeat_interval` INT(11) NULL DEFAULT NULL COMMENT 'minutes',
	`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`attempts` SMALLINT(6) NOT NULL,
	`deleted` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `id_post` (`id_post`),
	KEY `status` (`status`),
	KEY `deleted` (`deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;