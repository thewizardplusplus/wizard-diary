START TRANSACTION;

DELETE FROM `diary_accesses`;
ALTER TABLE `diary_accesses` AUTO_INCREMENT = 1;

DELETE FROM `diary_backups`;
ALTER TABLE `diary_backups` AUTO_INCREMENT = 1;

DELETE FROM `diary_daily_points`;
ALTER TABLE `diary_daily_points` AUTO_INCREMENT = 1;
INSERT INTO `diary_daily_points` (`text`, `order`)
VALUES
	('чистка лица в 00:00;', 3),
	('чистка лица в 06:00;', 5),
	('чистка лица в 12:00;', 7),
	('чистка лица в 18:00;', 9),
	('гигиена перед сном;', 11),
	('гигиена после пробуждения;', 13),
	('бритьё;', 15),
	('мытьё головы;', 17),
	('мытьё тела;', 19);

DELETE FROM `diary_imports`;
ALTER TABLE `diary_imports` AUTO_INCREMENT = 1;

DELETE FROM `diary_parameters`;
ALTER TABLE `diary_parameters` AUTO_INCREMENT = 1;

DELETE FROM `diary_points`;
ALTER TABLE `diary_points` AUTO_INCREMENT = 1;

DELETE FROM `diary_sessions`;
ALTER TABLE `diary_sessions` AUTO_INCREMENT = 1;

COMMIT;
