// create a new table called "dbusers"
CREATE TABLE IF NOT EXISTS dbusers (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL UNIQUE,
	password VARCHAR(255) NOT NULL UNIQUE,
	nickname VARCHAR(100) NULL,
	gender ENUM('male', 'female', 'other') NULL,
	interests TEXT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
