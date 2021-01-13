CREATE TABLE IF NOT EXISTS `sql_cache_entries` (
  `scope` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `value` longtext NOT NULL,
  `validUntil` bigint(20) unsigned NOT NULL,
  KEY `scope` (`scope`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
