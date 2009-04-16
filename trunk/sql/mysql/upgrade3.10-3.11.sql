
CREATE TABLE `mrpdemandtypes` (
  `mrpdemandtype` varchar(6) NOT NULL default '',
  `description` char(30) NOT NULL default '',
  PRIMARY KEY  (`mrpdemandtype`),
  KEY `mrpdemandtype` (`mrpdemandtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `mrpdemands` (
  `demandid` int(11) NOT NULL auto_increment,
  `stockid` varchar(20) NOT NULL default '',
  `mrpdemandtype` varchar(6) NOT NULL default '',
  `quantity` double NOT NULL default '0',
  `duedate` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`demandid`),
  KEY `StockID` (`stockid`),
  CONSTRAINT `mrpdemands_ibfk_1` FOREIGN KEY (`mrpdemandtype`) REFERENCES `mrpdemandtypes` (`mrpdemandtype`),
  CONSTRAINT `mrpdemands_ibfk_2` FOREIGN KEY (`stockid`) REFERENCES `stockmaster` (`stockid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `stockmaster` ADD `pansize` double NOT NULL default '0',
  						  ADD `shrinkfactor` double NOT NULL default '0';
  
CREATE TABLE mrpcalendar (
	calendardate date NOT NULL,
	daynumber int(6) NOT NULL,
	manufacturingflag smallint(6) NOT NULL default "1",
	INDEX (daynumber),
	PRIMARY KEY (calendardate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO mrpdemandtypes (mrpdemandtype,description) VALUES ('FOR','Forecast');

ALTER TABLE `geocode_param` add PRIMARY KEY (`geocodeid`);
ALTER TABLE `geocode_param` CHANGE `geocodeid` `geocodeid` TINYINT( 4 ) NOT NULL AUTO_INCREMENT;

INSERT INTO `factorcompanies` ( `id` , `coyname` ) VALUES (null, "None");

ALTER TABLE `custcontacts` CHANGE `role` `role` VARCHAR( 40 ) NOT NULL;
ALTER TABLE `custcontacts` CHANGE `phoneno` `phoneno` VARCHAR( 20 ) NOT NULL;
ALTER TABLE `custcontacts` CHANGE `notes` `notes` VARCHAR( 255 ) NOT NULL;

ALTER TABLE `purchdata` DROP PRIMARY KEY;
ALTER TABLE `purchdata` ADD PRIMARY KEY (`supplierno`,`stockid`, `effectivefrom`); 

ALTER TABLE `salesorders` ADD `quotedate` date NOT NULL default '0000-00-00';
ALTER TABLE `salesorders` ADD `confirmeddate` date NOT NULL default '0000-00-00';
