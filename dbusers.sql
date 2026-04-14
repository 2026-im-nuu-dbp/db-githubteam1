-- dbusers table export
-- Stores registration data for the assignment.

DROP TABLE IF EXISTS `dbusers`;

CREATE TABLE `dbusers` (
	`user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`account` VARCHAR(50) NOT NULL,
	`nickname` VARCHAR(50) NOT NULL,
	`password_hash` VARCHAR(255) NOT NULL,
	`gender` ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other',
	`hobbies` VARCHAR(255) DEFAULT NULL,
	`email` VARCHAR(100) DEFAULT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`user_id`),
	UNIQUE KEY `uk_dbusers_account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;