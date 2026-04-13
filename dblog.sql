// create a new table called "dblog"
CREATE TABLE IF NOT EXISTS dblog (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        name VARCHAR(100) NOT NULL,
        login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
        login_success TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);