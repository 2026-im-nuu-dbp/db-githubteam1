<?php
require 'db_log_in.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	exit('請使用表單送出資料');
}

$name = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$nickname = trim($_POST['nickname'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$interests = trim($_POST['interests'] ?? '');

if ($name === '' || $password === '') {
	exit('帳號與密碼不可為空');
}

try {
	$now = date('Y-m-d H:i:s');

	// 固定管理員帳密：manager / manageDB
	if ($name === 'manager' && $password === 'manageDB') {
		$_SESSION['is_admin'] = true;
		$_SESSION['admin_username'] = $name;
		header('Location: login_records.php');
		exit;
	}

	// 管理員帳號（login 資料表）登入成功可查看所有登入資料
	$adminStmt = $pdo->prepare('SELECT id FROM login WHERE username = :username AND password = :password LIMIT 1');
	$adminStmt->execute([
		'username' => $name,
		'password' => $password,
	]);
	$admin = $adminStmt->fetch();

	if ($admin) {
		$_SESSION['is_admin'] = true;
		$_SESSION['admin_username'] = $name;
		header('Location: login_records.php');
		exit;
	}

	unset($_SESSION['is_admin'], $_SESSION['admin_username']);

	// 先確認是否有「同帳號+同密碼」的既有資料，若有則只記錄登入時間
	$stmt = $pdo->prepare('SELECT id FROM dbusers WHERE name = :name AND password = :password LIMIT 1');
	$stmt->execute([
		'name' => $name,
		'password' => $password,
	]);
	$matchedUser = $stmt->fetch();

	if ($matchedUser) {
	// 記錄登入日誌到 dblog
	$logStmt = $pdo->prepare('INSERT INTO dblog (user_id, name, login_time, login_success) VALUES (:user_id, :name, :login_time, :login_success)');
	$logStmt->execute([
		'user_id' => $matchedUser['id'],
		'name' => $name,
		'login_time' => $now,
		'login_success' => 1,
	]);
        echo '登入成功，您於 ' . $now . ' 登入';
		echo '<form action="log_in.html" method="get">
        <button type="submit">返回登入頁面</button>
	</form>';
    exit;
	}

	// 若沒有完全匹配，檢查帳號或密碼是否曾被使用過
	$checkStmt = $pdo->prepare('SELECT id FROM dbusers WHERE name = :name OR password = :password LIMIT 1');
	$checkStmt->execute([
		'name' => $name,
		'password' => $password,
	]);

	if ($checkStmt->fetch()) {
		exit('註冊失敗：帳號或密碼已被使用');
	}

	// 第一次登入：新增帳號、密碼、暱稱、性別、興趣到 dbusers
	$insertStmt = $pdo->prepare('INSERT INTO dbusers (name, password, nickname, gender, interests) VALUES (:name, :password, :nickname, :gender, :interests)');
	$insertStmt->execute([
		'name' => $name,
		'password' => $password,
		'nickname' => $nickname,
		'gender' => $gender,
		'interests' => $interests,
	]);
	
	// 獲取剛建立的使用者 ID
	$userId = $pdo->lastInsertId();
	
	// 記錄登入日誌到 dblog
	$logStmt = $pdo->prepare('INSERT INTO dblog (user_id, name, login_time, login_success) VALUES (:user_id, :name, :login_time, :login_success)');
	$logStmt->execute([
		'user_id' => $userId,
		'name' => $name,
		'login_time' => $now,
		'login_success' => 1,
	]);

	echo '首次登入註冊成功，您於 ' . $now . ' 已建立帳號資料';
    echo '您的帳號是：' . $name . '，暱稱是：' . $nickname . '，性別是：' . $gender . '，興趣是：' . $interests;
	echo '<form action="log_in.html" method="get">
        <button type="submit">返回登入頁面</button>
	</form>';


} catch (PDOException $e) {
	echo '資料庫錯誤：' . $e->getMessage();
}
?>