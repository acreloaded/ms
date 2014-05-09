-- AssaultCube Reloaded Master-Server Tables

CREATE TABLE IF NOT EXISTS `acrms_allow_ip` (
  `ipl` int(10) unsigned NOT NULL,
  `ipr` int(10) unsigned NOT NULL,
  `owner` bigint(20) unsigned NOT NULL,
  `reason` varchar(32) NOT NULL,
  PRIMARY KEY (`ipl`,`ipr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acrms_auth` (
  `ip` int(10) unsigned NOT NULL,
  `port` smallint(5) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  `hash` char(40) NOT NULL,
  `salt` char(25) NOT NULL,
  `uid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ip`,`port`,`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acrms_bans_ip` (
  `ipl` int(10) unsigned NOT NULL,
  `ipr` int(10) unsigned NOT NULL,
  `owner` bigint(20) unsigned NOT NULL,
  `reason` varchar(32) NOT NULL,
  PRIMARY KEY (`ipl`,`ipr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acrms_cache` (
  `key` varchar(64) NOT NULL,
  `val` varchar(1024) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acrms_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `time` datetime NOT NULL,
  `level` varchar(5) NOT NULL,
  `content` varchar(512) NOT NULL,
  `issue` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `acrms_servers` (
  `ip` int(10) unsigned NOT NULL,
  `port` smallint(5) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  `proto` int(11) NOT NULL,
  `failures` tinyint(4) unsigned NOT NULL,
  `authtime` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ip`,`port`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acrms_servers_trans` (
  `ip` int(10) unsigned NOT NULL,
  `domain` varchar(63) NOT NULL,
  `port` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`ip`,`domain`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acrms_servers_weights` (
  `ip` int(10) unsigned NOT NULL,
  `port` smallint(5) unsigned NOT NULL,
  `weight` smallint(6) NOT NULL,
  PRIMARY KEY (`ip`,`port`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `acrms_settings` (
  `key` varchar(64) NOT NULL,
  `val` varchar(255) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `acrms_settings` (`key`, `val`) VALUES
('minprotocol', '135'),
('maxport', '65534'),
('placeholder', 'no-servers--please-run-one'),
('defaultport', '28770'),
('minport', '0'),
('currentgame', '20508');

CREATE TABLE IF NOT EXISTS `acrms_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usr` varchar(15) NOT NULL,
  `pwd` varchar(128) NOT NULL,
  `priv` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usr` (`usr`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

CREATE TABLE IF NOT EXISTS `acrms_users_sessions` (
  `key` char(40) NOT NULL,
  `uid` bigint(20) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
