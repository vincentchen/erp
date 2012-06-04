ALTER TABLE `stockmaster` CHANGE `lastcostupdate` `lastcostupdate` DATE;
ALTER TABLE paymentmethods ADD opencashdrawer tinyint NOT NULL default '0';
INSERT INTO scripts VALUES('Z_AccountCodeTo20Digits.php',15,'Update account code to 20 digits to meet requirements for long account code in some countris');
UPDATE config SET confvalue='4.07.10' WHERE confname='VersionNumber';
