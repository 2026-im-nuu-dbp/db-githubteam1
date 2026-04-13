<?php
session_start();

$host = 'localhost';
$db   = 'db_a01';    
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$dsnNoDb = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // 錯誤碼 1049 代表資料庫不存在，這裡自動建立後再重連。
    if ((int) $e->getCode() === 1049) {
        $bootstrapPdo = new PDO($dsnNoDb, $user, $pass, $options);
        $bootstrapPdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET $charset COLLATE utf8mb4_unicode_ci");

        $pdo = new PDO($dsn, $user, $pass, $options);
    } else {
        throw new \PDOException($e->getMessage(), (int) $e->getCode());
    }
}

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS dbusers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL UNIQUE,
        nickname VARCHAR(100) NULL,
        gender ENUM('male', 'female', 'other') NULL,
        interests TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS dblog (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        name VARCHAR(100) NOT NULL,
        login_time DATETIME DEFAULT CURRENT_TIMESTAMP,
        login_success TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);
?>