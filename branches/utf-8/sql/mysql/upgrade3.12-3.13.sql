ALTER TABLE `www_users` ADD COLUMN `pdflanguage` tinyint(1) NOT NULL DEFAULT '0';
ALTER TABLE `cogsglposting` CHANGE `area` CHAR(3) NOT NULL DEFAULT '';
