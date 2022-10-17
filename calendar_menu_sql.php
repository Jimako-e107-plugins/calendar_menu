CREATE TABLE `event_cat` (
  `event_cat_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `event_cat_name` varchar(100) NOT NULL DEFAULT '',
  `event_cat_icon` varchar(100) NOT NULL DEFAULT '',
  `event_cat_class` int(10) unsigned NOT NULL DEFAULT 0,
  `event_cat_subs` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `event_cat_ahead` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `event_cat_msg1` text DEFAULT NULL,
  `event_cat_msg2` text DEFAULT NULL,
  `event_cat_notify` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `event_cat_last` int(10) unsigned NOT NULL DEFAULT 0,
  `event_cat_today` int(10) unsigned NOT NULL DEFAULT 0,
  `event_cat_lastupdate` int(10) unsigned NOT NULL DEFAULT 0,
  `event_cat_addclass` int(10) unsigned NOT NULL DEFAULT 0,
  `event_cat_description` text DEFAULT NULL,
  `event_cat_force_class` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`event_cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;