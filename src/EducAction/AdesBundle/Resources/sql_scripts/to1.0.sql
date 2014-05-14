CREATE TABLE  `ades_config` (
  `con_id` int(11) NOT NULL AUTO_INCREMENT,
  `con_key` varchar(45) NOT NULL,
  `con_value` varchar(45) NOT NULL,
  `con_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`con_id`) USING BTREE,
  UNIQUE KEY `unique_key` (`con_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='valeurs de configuration'
