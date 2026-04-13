<?php
$host = '127.0.0.1';
$db   = 'db_a01';
$user = 'root';
$pass = ''; // Laragon 預設密碼為空
$charset = 'utf8mb4';//編碼

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // 建立連接
    $pdo = new PDO($dsn, $user, $pass);//PDO = PHP Data Objects，提供一個統一的介面來存取不同的資料庫
    echo "✅ 資料庫連線成功！";

    // 嘗試抓取資料
    $stmt = $pdo->query("SELECT *  FROM dbusers");
    while ($row = $stmt->fetch()) {
        echo "<p>使用者姓名：" . $row['name'] ."  email：".$row['email']."</p>";
    }

} catch (PDOException $e) {
    echo "❌ 連線失敗: " . $e->getMessage();
}
?>