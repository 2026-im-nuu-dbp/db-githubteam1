-- dblog table export
-- Stores login log data, including account, timestamp, and success status.

DROP TABLE IF EXISTS `dblog`;

CREATE TABLE `dblog` (
	`log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_account` VARCHAR(50) NOT NULL,
	`user_id` INT UNSIGNED DEFAULT NULL,
	`login_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`is_success` TINYINT(1) NOT NULL DEFAULT 0,
	`ip_address` VARCHAR(45) DEFAULT NULL,
	`message` VARCHAR(255) DEFAULT NULL,
	PRIMARY KEY (`log_id`),
	KEY `idx_dblog_user_account` (`user_account`),
	KEY `idx_dblog_login_time` (`login_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;