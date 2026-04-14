<?php
require __DIR__ . '/db.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account = trim($_POST['account'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT user_id, account, nickname, password_hash FROM dbusers WHERE account = :account LIMIT 1');
    $stmt->execute(['account' => $account]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'user_id' => (int) $user['user_id'],
            'account' => $user['account'],
            'nickname' => $user['nickname'],
        ];
        login_attempt($account, (int) $user['user_id'], true, '登入成功');
        flash_set('success', '歡迎回來，登入成功。');
        header('Location: memo.php');
        exit;
    }

    login_attempt($account, $user ? (int) $user['user_id'] : null, false, '登入失敗');
    flash_set('error', '帳號或密碼錯誤。');
}

render_header('會員登入', 'login');
?>
<section class="section-box">
    <div class="section-head">
        <div>
            <h2>登入帳號</h2>
            <p>登入後即可新增、修改與刪除你的生活分享。</p>
        </div>
    </div>

    <form method="post" class="form-grid" style="max-width: 720px;">
        <div class="field full">
            <label>帳號</label>
            <input type="text" name="account" required>
        </div>
        <div class="field full">
            <label>密碼</label>
            <input type="password" name="password" required>
        </div>
        <div class="full actions">
            <button class="btn" type="submit">登入</button>
            <a class="ghost-btn" href="register.php">前往註冊</a>
        </div>
    </form>
</section>
<?php render_footer(); ?>