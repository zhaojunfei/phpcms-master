DROP TABLE IF EXISTS `v9_demo`;
CREATE TABLE IF NOT EXISTS `v9_demo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `inputtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
