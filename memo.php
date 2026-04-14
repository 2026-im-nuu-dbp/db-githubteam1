<?php
require __DIR__ . '/db.php';

$pdo = db();

if (!is_logged_in()) {
    flash_set('error', '請先登入才能管理生活備忘。');
    header('Location: login.php');
    exit;
}

$user = current_user();
$action = $_GET['action'] ?? '';
$editMemo = null;

if ($action === 'delete' && isset($_GET['id'])) {
    $memoId = (int) $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM dbmemo WHERE memo_id = :memo_id AND creator_id = :creator_id AND is_deleted = 0 LIMIT 1');
    $stmt->execute(['memo_id' => $memoId, 'creator_id' => $user['user_id']]);
    $memo = $stmt->fetch();

    if ($memo) {
        delete_file_if_exists($memo['image_path']);
        delete_file_if_exists($memo['thumbnail_path']);
        $delete = $pdo->prepare('UPDATE dbmemo SET is_deleted = 1 WHERE memo_id = :memo_id');
        $delete->execute(['memo_id' => $memoId]);
        flash_set('success', '備忘已刪除。');
    } else {
        flash_set('error', '找不到可刪除的資料。');
    }

    header('Location: memo.php');
    exit;
}

if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM dbmemo WHERE memo_id = :memo_id AND creator_id = :creator_id AND is_deleted = 0 LIMIT 1');
    $stmt->execute(['memo_id' => (int) $_GET['id'], 'creator_id' => $user['user_id']]);
    $editMemo = $stmt->fetch();

    if (!$editMemo) {
        flash_set('error', '找不到可編輯的資料。');
        header('Location: memo.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memoId = (int) ($_POST['memo_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        flash_set('error', '標題與內容不能空白。');
        header($memoId > 0 ? 'Location: memo.php?action=edit&id=' . $memoId : 'Location: memo.php');
        exit;
    }

    try {
        if ($memoId > 0) {
            $stmt = $pdo->prepare('SELECT * FROM dbmemo WHERE memo_id = :memo_id AND creator_id = :creator_id AND is_deleted = 0 LIMIT 1');
            $stmt->execute(['memo_id' => $memoId, 'creator_id' => $user['user_id']]);
            $memo = $stmt->fetch();

            if (!$memo) {
                throw new RuntimeException('找不到可更新的資料。');
            }

            $imagePath = $memo['image_path'];
            $thumbPath = $memo['thumbnail_path'];

            if (!empty($_FILES['image']['name'])) {
                [$newImagePath, $newThumbPath] = upload_memo_image($_FILES['image']);
                delete_file_if_exists($imagePath);
                delete_file_if_exists($thumbPath);
                $imagePath = $newImagePath;
                $thumbPath = $newThumbPath;
            }

            $update = $pdo->prepare('UPDATE dbmemo SET title = :title, content = :content, image_path = :image_path, thumbnail_path = :thumbnail_path WHERE memo_id = :memo_id AND creator_id = :creator_id');
            $update->execute([
                'title' => $title,
                'content' => $content,
                'image_path' => $imagePath,
                'thumbnail_path' => $thumbPath,
                'memo_id' => $memoId,
                'creator_id' => $user['user_id'],
            ]);

            flash_set('success', '備忘已更新。');
        } else {
            [$imagePath, $thumbPath] = upload_memo_image($_FILES['image'] ?? []);

            $insert = $pdo->prepare('INSERT INTO dbmemo (creator_id, title, content, image_path, thumbnail_path) VALUES (:creator_id, :title, :content, :image_path, :thumbnail_path)');
            $insert->execute([
                'creator_id' => $user['user_id'],
                'title' => $title,
                'content' => $content,
                'image_path' => $imagePath,
                'thumbnail_path' => $thumbPath,
            ]);

            flash_set('success', '已新增生活備忘。');
        }
    } catch (Throwable $throwable) {
        flash_set('error', $throwable->getMessage());
    }

    header('Location: memo.php');
    exit;
}

$memos = $pdo->query('SELECT m.*, u.nickname, u.account FROM dbmemo m INNER JOIN dbusers u ON u.user_id = m.creator_id WHERE m.is_deleted = 0 ORDER BY m.created_at DESC, m.memo_id DESC')->fetchAll();

render_header('生活備忘', 'memo');
?>
<section class="section-box">
    <div class="section-head">
        <div>
            <h2><?= $editMemo ? '編輯備忘' : '新增備忘' ?></h2>
            <p>記錄一段生活文字，並上傳一張圖片，系統會自動建立縮圖。</p>
        </div>
    </div>

    <form method="post" enctype="multipart/form-data" class="form-grid">
        <input type="hidden" name="memo_id" value="<?= e((string) ($editMemo['memo_id'] ?? '')) ?>">
        <div class="field full">
            <label>標題</label>
            <input type="text" name="title" value="<?= e($editMemo['title'] ?? '') ?>" placeholder="今天的咖啡時光" required>
        </div>
        <div class="field full">
            <label>內容</label>
            <textarea name="content" placeholder="多行文字可以直接輸入，記錄今天發生的事情。" required><?= e($editMemo['content'] ?? '') ?></textarea>
        </div>
        <div class="field full">
            <label>圖片</label>
            <input type="file" name="image" accept="image/*" <?= $editMemo ? '' : 'required' ?>>
            <p class="note">新增時必填，編輯時若不重新上傳圖片會沿用原圖。</p>
        </div>
        <div class="full actions">
            <button class="btn" type="submit"><?= $editMemo ? '更新備忘' : '新增備忘' ?></button>
            <?php if ($editMemo): ?>
                <a class="ghost-btn" href="memo.php">取消編輯</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="section-box">
    <div class="section-head">
        <div>
            <h2>所有生活記錄</h2>
            <p>這裡可查看、修改與刪除所有已公開的備忘。</p>
        </div>
    </div>

    <?php if (!$memos): ?>
        <p class="note">目前沒有任何備忘，先新增一筆吧。</p>
    <?php else: ?>
        <div class="memo-grid">
            <?php foreach ($memos as $memo): ?>
                <article class="memo-card">
                    <img class="memo-thumb" src="<?= e($memo['thumbnail_path']) ?>" alt="<?= e($memo['title']) ?>">
                    <div class="memo-body">
                        <div class="memo-meta">作者：<?= e($memo['nickname']) ?> (<?= e($memo['account']) ?>) · <?= e($memo['created_at']) ?></div>
                        <h3><?= e($memo['title']) ?></h3>
                        <div class="memo-content"><?= e($memo['content']) ?></div>
                        <div class="actions">
                            <a class="btn" href="memo.php?action=edit&id=<?= (int) $memo['memo_id'] ?>">編輯</a>
                            <a class="ghost-btn danger" href="memo.php?action=delete&id=<?= (int) $memo['memo_id'] ?>" onclick="return confirm('確定要刪除這筆備忘嗎？');">刪除</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php render_footer(); ?>