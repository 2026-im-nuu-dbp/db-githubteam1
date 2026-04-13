<?php
require 'db.php';
// 1. 準備 SQL 語法
$stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");

$name=$_POST['name'];
$email=$_POST['email'];
$id=$_POST['id'];

// 2. 執行並綁定參數
$stmt->execute(['name' => $name, 'email' => $email, 'id' => $id]);

// 3. 準備 SQL 語法 (問號占位符)
//$stmt = $pdo->prepare("UPDATE users SET name = ? , email = ? WHERE id = ?");
// 4. 執行並綁定參數 依照順序
//$stmt->execute(['Jane', 'jane@example.com', 3]);


// 5. 顯示所有使用者
$sql = "SELECT * FROM users";
$users = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $user) {
    echo "<p>{$user['name']} ({$user['email']})</p>";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post" action="db4.php">
    <label>要更新的使用者姓名：</label> 
    <input type="text" name="name" placeholder="Name"><br>

    <label>要更新的使用者 Email：</label>
    <input type="text" name="email" placeholder="Email"><br>

    <label>要更新的使用者 ID：</label>
    <input type="text" name="id" placeholder="User ID"><br>

    <button type="submit">Update User</button>
</form>

</body>
</html>