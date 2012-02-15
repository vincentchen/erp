INSERT INTO securitytokens VALUES(0, 'Main Index Page');
INSERT INTO securitygroups (SELECT secroleid,0 FROM securityroles);

INSERT INTO `scripts` (`script` ,`pagesecurity` ,`description`) VALUES ('reportwriter/admin/ReportCreator.php', '15', 'Report Writer');
INSERT INTO `scripts` (`script` ,`pagesecurity` ,`description`) VALUES ('RecurringSalesOrdersProcess.php', '1', 'Process Recurring Sales Orders');

UPDATE config SET confvalue='4.08.0' WHERE confname='VersionNumber';