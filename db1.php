<?php
require 'db.php';
// 1. 準備 SQL 語法
$stmt = $pdo->prepare("SELECT * FROM dbusers WHERE id = :id");

// 2. 執行並綁定參數
$stmt->execute(['id' => 1]);

// 3. 取出結果
$user = $stmt->fetch();

print_r($user);

// 4. 顯示所有使用者
$sql = "SELECT * FROM dbusers";
$users = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $user) {
    echo "<p>{$user['name']} ({$user['email']})</p>";
}
?>