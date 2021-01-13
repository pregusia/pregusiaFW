
CREATE TABLE IF NOT EXISTS `cron_delayed_events` (
  `eventID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `runTime` int(10) unsigned NOT NULL,
  `eventType` varchar(128) NOT NULL,
  `eventData` longtext NOT NULL,
  PRIMARY KEY (`eventID`),
  KEY `runTime` (`runTime`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

