DROP TABLE IF EXISTS `v9_booking`;
CREATE TABLE IF NOT EXISTS `v9_booking` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(20) NOT NULL DEFAULT '',
  `booking_time` varchar(20) NOT NULL DEFAULT '',
  `remark` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
