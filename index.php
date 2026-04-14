<?php
require __DIR__ . '/db.php';

$pdo = db();
$user = current_user();

$userCount = (int) $pdo->query('SELECT COUNT(*) AS total FROM dbusers')->fetch()['total'];
$memoCount = (int) $pdo->query('SELECT COUNT(*) AS total FROM dbmemo WHERE is_deleted = 0')->fetch()['total'];
$logCount = (int) $pdo->query('SELECT COUNT(*) AS total FROM dblog')->fetch()['total'];

if ($user) {
    $recentStmt = $pdo->prepare('SELECT m.memo_id, m.title, m.content, m.image_path, m.thumbnail_path, m.created_at, u.nickname, u.account FROM dbmemo m INNER JOIN dbusers u ON u.user_id = m.creator_id WHERE m.is_deleted = 0 AND m.creator_id = :creator_id ORDER BY m.created_at DESC, m.memo_id DESC LIMIT 6');
    $recentStmt->execute(['creator_id' => $user['user_id']]);
    $recentMemos = $recentStmt->fetchAll();
} else {
    $recentMemos = [];
}

render_header('日常圖文記錄站', 'home');
?>
<section class="hero">
    <div class="hero-card">
        <div class="eyebrow">生活分享記錄網站</div>
        <h1>把每天的照片、文字與登入紀錄，整理成可以展示的故事。</h1>
        <p>這個網站對應作業要求的三張資料表，提供註冊、登入、登入紀錄瀏覽，以及圖文備忘 CRUD。你可以把它當成一個簡潔版的生活分享平台，記錄今天吃了什麼、去了哪裡、拍了什麼。</p>
        <div class="hero-actions">
            <a class="btn" href="register.php">開始註冊</a>
            <a class="ghost-btn" href="memo.php">查看備忘</a>
        </div>
    </div>

    <div class="hero-side">
        <div class="stat-grid">
            <div class="stat-card">
                <span class="muted">註冊成員</span>
                <strong><?= $userCount ?></strong>
            </div>
            <div class="stat-card">
                <span class="muted">生活備忘</span>
                <strong><?= $memoCount ?></strong>
            </div>
            <div class="stat-card">
                <span class="muted">登入紀錄</span>
                <strong><?= $logCount ?></strong>
            </div>
        </div>

        <div class="section-box">
            <div class="section-head">
                <div>
                    <h2>網站功能</h2>
                    <p>註冊、登入、圖文記錄、登入日誌一站完成。</p>
                </div>
            </div>
            <div class="actions">
                <span class="badge">帳號註冊</span>
                <span class="badge">登入驗證</span>
                <span class="badge">圖片縮圖</span>
                <span class="badge">登入瀏覽</span>
            </div>
        </div>
    </div>
</section>

<section class="section-box">
    <div class="section-head">
        <div>
            <h2>最新生活記錄</h2>
            <p><?= $user ? '這裡只會顯示你最近新增的圖文備忘。' : '請先登入後查看你的圖文備忘。' ?></p>
        </div>
        <a class="btn" href="memo.php">新增備忘</a>
    </div>

    <?php if (!$recentMemos): ?>
        <p class="note">目前還沒有備忘資料，先註冊並登入後新增第一筆生活分享。</p>
    <?php else: ?>
        <div class="memo-grid">
            <?php foreach ($recentMemos as $memo): ?>
                <article class="memo-card">
                    <img class="memo-thumb" src="<?= e($memo['thumbnail_path']) ?>" alt="<?= e($memo['title']) ?>">
                    <div class="memo-body">
                        <div class="memo-meta"><?= e($memo['nickname']) ?> · <?= e($memo['created_at']) ?></div>
                        <h3><?= e($memo['title']) ?></h3>
                        <div class="memo-content"><?= e(excerpt_text($memo['content'], 140)) ?></div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php render_footer(); ?>