<?php
require 'db.php';
// 1. 準備 SQL 語法
$stmt = $pdo->prepare("DELETE FROM users WHERE name = :name");

$name=$_POST['name'];

// 2. 執行並綁定參數
$stmt->execute(['name' => $name]);

// 3. 準備 SQL 語法 (問號占位符)
//$stmt = $pdo->prepare("DELETE FROM users WHERE name = ?");
// 4. 執行並綁定參數 依照順序
//$stmt->execute(['Jane']);   


// 5. 顯示所有使用者
$sql = "SELECT * FROM users";
$users = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $user) {
    echo "<p>{$user['name']} ({$user['email']})</p>";
}
?>