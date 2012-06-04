ALTER TABLE `stockmaster` CHANGE `lastcostupdate` `lastcostupdate` DATE;
ALTER TABLE paymentmethods ADD opencashdrawer tinyint NOT NULL default '0';
UPDATE config SET confvalue='4.07.10' WHERE confname='VersionNumber';
