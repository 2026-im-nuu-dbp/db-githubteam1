-- dbmemo table export
-- Stores image-and-text memo data for the assignment.

DROP TABLE IF EXISTS `dbmemo`;

CREATE TABLE `dbmemo` (
	`memo_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`creator_id` INT UNSIGNED NOT NULL,
	`title` VARCHAR(100) NOT NULL,
	`content` TEXT NOT NULL,
	`image_path` VARCHAR(255) NOT NULL,
	`thumbnail_path` VARCHAR(255) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY (`memo_id`),
	KEY `idx_dbmemo_creator_id` (`creator_id`),
	KEY `idx_dbmemo_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;