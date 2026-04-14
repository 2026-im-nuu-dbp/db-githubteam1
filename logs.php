<?php
require __DIR__ . '/db.php';

if (!is_logged_in()) {
    flash_set('error', '請先登入才能查看登入紀錄。');
    header('Location: login.php');
    exit;
}

$pdo = db();
$records = $pdo->query('SELECT log_id, user_account, user_id, login_time, is_success, ip_address, message FROM dblog ORDER BY login_time DESC, log_id DESC')->fetchAll();

render_header('登入紀錄', 'logs');
?>
<section class="section-box">
    <div class="section-head">
        <div>
            <h2>登入紀錄瀏覽</h2>
            <p>可查看所有帳號的登入時間、成功失敗與來源 IP。</p>
        </div>
    </div>

    <?php if (!$records): ?>
        <p class="note">目前還沒有登入紀錄。</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>帳號</th>
                    <th>使用者 ID</th>
                    <th>登入時間</th>
                    <th>結果</th>
                    <th>IP</th>
                    <th>訊息</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?= e((string) $row['log_id']) ?></td>
                        <td><?= e($row['user_account']) ?></td>
                        <td><?= e((string) ($row['user_id'] ?? '')) ?></td>
                        <td><?= e($row['login_time']) ?></td>
                        <td><span class="badge"><?= (int) $row['is_success'] === 1 ? '成功' : '失敗' ?></span></td>
                        <td><?= e((string) ($row['ip_address'] ?? '')) ?></td>
                        <td><?= e((string) ($row['message'] ?? '')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php render_footer(); ?>