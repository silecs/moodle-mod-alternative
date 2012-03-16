--
-- Table existant déjà dans Moodle
--

CREATE TABLE IF NOT EXISTS `mdl_course_modules` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `module` bigint(10) unsigned NOT NULL DEFAULT '0',
  `instance` bigint(10) unsigned NOT NULL DEFAULT '0',
  `section` bigint(10) unsigned NOT NULL DEFAULT '0',
  `idnumber` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `added` bigint(10) unsigned NOT NULL DEFAULT '0',
  `score` smallint(4) NOT NULL DEFAULT '0',
  `indent` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `visibleold` tinyint(1) NOT NULL DEFAULT '1',
  `groupmode` smallint(4) NOT NULL DEFAULT '0',
  `groupingid` bigint(10) unsigned NOT NULL DEFAULT '0',
  `groupmembersonly` smallint(4) unsigned NOT NULL DEFAULT '0',
  `completion` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `completiongradeitemnumber` bigint(10) unsigned DEFAULT NULL,
  `completionview` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `completionexpected` bigint(10) unsigned NOT NULL DEFAULT '0',
  `availablefrom` bigint(10) unsigned NOT NULL DEFAULT '0',
  `availableuntil` bigint(10) unsigned NOT NULL DEFAULT '0',
  `showavailability` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mdl_courmodu_vis_ix` (`visible`),
  KEY `mdl_courmodu_cou_ix` (`course`),
  KEY `mdl_courmodu_mod_ix` (`module`),
  KEY `mdl_courmodu_ins_ix` (`instance`),
  KEY `mdl_courmodu_idncou_ix` (`idnumber`,`course`),
  KEY `mdl_courmodu_gro_ix` (`groupingid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='course_modules table retrofitted from MySQL' AUTO_INCREMENT=1 ;

--
-- Nouvelles tables du module
--

CREATE TABLE IF NOT EXISTS `mod_alternative` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `course` bigint(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title',
  `intro` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Description',
  `teammin` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'min size of a team',
  `teammax` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0 if no registration by team',
  `multiplemin` smallint(4) unsigned NOT NULL DEFAULT '1' COMMENT 'has to register in at least ...',
  `multiplemax` smallint(4) unsigned NOT NULL DEFAULT '1' COMMENT '0 if registering to everything is allowed',
  `changeallowed` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 if the user can change its choice',
  `notify` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `showreg` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `showgroupreg` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `timemodified` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mod_inscr_cou_ix` (`course`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


CREATE TABLE IF NOT EXISTS `mod_alternative_options` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `alternative` bigint(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title',
  `intro` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Description',
  `when` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Date or date interval',
  `avail` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'number of available registrations',
  `groupdep` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 if group dependent',
  `timemodified` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mod_inscr_options_inscr_ix` (`alternative`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


CREATE TABLE IF NOT EXISTS `mod_alternative_optionsgroup` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `group` bigint(10) unsigned NOT NULL DEFAULT '0',
  `option` bigint(10) unsigned NOT NULL DEFAULT '0',
  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 if users from this group cannot access this option',
  `intro` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Description (overloaded)',
  `when` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Date or date interval (overloaded)',
  `avail` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'number of available registrations (overloaded)',
  `timemodified` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mod_inscr_og_gr_ix` (`group`),
  KEY `mod_inscr_og_opt_ix` (`option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;


CREATE TABLE IF NOT EXISTS `mod_alternative_registration` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `option` bigint(10) unsigned NOT NULL DEFAULT '0',
  `user` bigint(10) unsigned NOT NULL DEFAULT '0',
  `teamleader` bigint(10) unsigned NOT NULL DEFAULT '0',
  `timemodified` bigint(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mod_inscr_reg_option_ix` (`option`),
  KEY `mod_inscr_reg_user_ix` (`user`),
  KEY `mod_inscr_reg_leader_ix` (`teamleader`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

