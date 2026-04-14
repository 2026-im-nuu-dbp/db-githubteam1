<?php
require __DIR__ . '/db.php';

$pdo = db();

$values = [
    'account' => '',
    'nickname' => '',
    'gender' => 'other',
    'hobbies' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['account'] = trim($_POST['account'] ?? '');
    $values['nickname'] = trim($_POST['nickname'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $values['gender'] = trim($_POST['gender'] ?? 'other');
    $values['hobbies'] = trim($_POST['hobbies'] ?? '');
    $values['email'] = trim($_POST['email'] ?? '');

    if ($values['account'] === '' || $values['nickname'] === '' || $password === '') {
        flash_set('error', '帳號、暱稱與密碼不可為空。');
    } else {
        $exists = $pdo->prepare('SELECT user_id FROM dbusers WHERE account = :account LIMIT 1');
        $exists->execute(['account' => $values['account']]);

        if ($exists->fetch()) {
            flash_set('error', '這個帳號已被使用，請換一個。');
        } else {
            $stmt = $pdo->prepare('INSERT INTO dbusers (account, nickname, password_hash, gender, hobbies, email) VALUES (:account, :nickname, :password_hash, :gender, :hobbies, :email)');
            $stmt->execute([
                'account' => $values['account'],
                'nickname' => $values['nickname'],
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'gender' => $values['gender'],
                'hobbies' => $values['hobbies'],
                'email' => $values['email'],
            ]);

            flash_set('success', '註冊完成，現在可以登入開始記錄生活。');
            header('Location: login.php');
            exit;
        }
    }
}

render_header('會員註冊', 'register');
?>
<section class="section-box">
    <div class="section-head">
        <div>
            <h2>註冊新帳號</h2>
            <p>輸入帳號、暱稱與基本資料，完成後就能登入。</p>
        </div>
    </div>

    <form method="post" class="form-grid">
        <div class="field">
            <label>帳號</label>
            <input type="text" name="account" value="<?= e($values['account']) ?>" required>
        </div>
        <div class="field">
            <label>暱稱</label>
            <input type="text" name="nickname" value="<?= e($values['nickname']) ?>" required>
        </div>
        <div class="field">
            <label>密碼</label>
            <input type="password" name="password" required>
        </div>
        <div class="field">
            <label>電子郵件</label>
            <input type="email" name="email" value="<?= e($values['email']) ?>">
        </div>
        <div class="field">
            <label>性別</label>
            <select name="gender">
                <option value="male" <?= $values['gender'] === 'male' ? 'selected' : '' ?>>男</option>
                <option value="female" <?= $values['gender'] === 'female' ? 'selected' : '' ?>>女</option>
                <option value="other" <?= $values['gender'] === 'other' ? 'selected' : '' ?>>其他</option>
            </select>
        </div>
        <div class="field">
            <label>興趣</label>
            <input type="text" name="hobbies" value="<?= e($values['hobbies']) ?>" placeholder="攝影、旅行、咖啡...">
        </div>
        <div class="full actions">
            <button class="btn" type="submit">完成註冊</button>
            <a class="ghost-btn" href="login.php">直接登入</a>
        </div>
    </form>
</section>
<?php render_footer(); ?>