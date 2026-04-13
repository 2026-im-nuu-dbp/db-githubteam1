<?php
require 'db.php';

//顯示所有使用者
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
<form method="post" action="db3.php">
    <label for="">要刪除的使用者姓名</label>
    <input type="text" name="name" placeholder="Name">
    <button type="submit">Delete User</button>
</form>
</body>
</html>