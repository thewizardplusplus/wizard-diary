-- for MySQL

CREATE TABLE `diary_points` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`date` DATE NOT NULL,
	`text` TEXT NOT NULL,
	`state` ENUM('INITIAL', 'SATISFIED', 'NOT_SATISFIED', 'CANCELED') NOT NULL DEFAULT 'INITIAL'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `diary_parameters` (
	`id` INT NOT NULL DEFAULT '1' PRIMARY KEY,
	`password_hash` TEXT NOT NULL,
	`points_on_page` INT NOT NULL DEFAULT '10'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `diary_parameters` (`password_hash`)
VALUES
	('$2a$13$7RC2CWHDqafP4dvl7t5PCucccPVl7spVT4FiALXEaxWCnzCTskqAK');
