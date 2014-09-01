CREATE TABLE  `ades_labels` (
  `lbl_id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `lbl_tag` VARCHAR(256) NOT NULL,
  `lbl_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `lbl_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lbl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='contains the labels for facts (#45)';

CREATE TABLE  `ades_fact_label` (
  `fl_id` int(11) NOT NULL AUTO_INCREMENT,
  `fl_fact_id` int(11) NOT NULL COMMENT 'references ades_faits.idfait',
  `fl_lbl_id` int(11) NOT NULL COMMENT 'references ades_labels.lbl_id',
  PRIMARY KEY (`fl_id`),
  KEY `indexFactId` (`fl_fact_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='link between facts and labels (#45)'
