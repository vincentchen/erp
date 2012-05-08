ALTER TABLE `stockmaster` CHANGE `lastcostupdate` `lastcostupdate` DATE NOT NULL DEFAULT '0000-00-00';
ALTER TABLE paymentmethods ADD opencashdrawer tinyint NOT NULL default '0';
UPDATE config SET confvalue='4.07.9' WHERE confname='VersionNumber';
