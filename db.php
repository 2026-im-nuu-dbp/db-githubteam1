<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Taipei');

session_start();

const DB_HOST = '127.0.0.1';
const DB_NAME = 'db_a01';
const DB_USER = 'root';
const DB_PASS = '';
const DB_CHARSET = 'utf8mb4';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $serverDsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
    $dbDsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    $serverPdo = new PDO($serverDsn, DB_USER, DB_PASS, $options);
    $serverPdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET ' . DB_CHARSET . ' COLLATE ' . DB_CHARSET . '_unicode_ci');

    $pdo = new PDO($dbDsn, DB_USER, DB_PASS, $options);
    ensure_schema($pdo);

    return $pdo;
}

function ensure_schema(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS dbusers (
            user_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            account VARCHAR(50) NOT NULL,
            nickname VARCHAR(50) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            gender ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other',
            hobbies VARCHAR(255) DEFAULT NULL,
            email VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id),
            UNIQUE KEY uk_dbusers_account (account)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS dblog (
            log_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_account VARCHAR(50) NOT NULL,
            user_id INT UNSIGNED DEFAULT NULL,
            login_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            is_success TINYINT(1) NOT NULL DEFAULT 0,
            ip_address VARCHAR(45) DEFAULT NULL,
            message VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (log_id),
            KEY idx_dblog_user_account (user_account),
            KEY idx_dblog_login_time (login_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS dbmemo (
            memo_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            creator_id INT UNSIGNED NOT NULL,
            title VARCHAR(100) NOT NULL,
            content TEXT NOT NULL,
            image_path VARCHAR(255) NOT NULL,
            thumbnail_path VARCHAR(255) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_deleted TINYINT(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (memo_id),
            KEY idx_dbmemo_creator_id (creator_id),
            KEY idx_dbmemo_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function excerpt_text(string $text, int $limit = 140): string
{
    if (function_exists('mb_strimwidth')) {
        return mb_strimwidth($text, 0, $limit, '...', 'UTF-8');
    }

    return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
}

function base_url(string $path = ''): string
{
    return $path;
}

function render_header(string $title, string $active = ''): void
{
    $user = current_user();
    $flash = flash_get();
    ?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="page-shell">
    <header class="topbar">
        <div>
            <div class="eyebrow">Life Share Record</div>
            <a class="brand" href="index.php">日常圖文記錄站</a>
        </div>
        <nav class="nav">
            <a class="<?= $active === 'home' ? 'active' : '' ?>" href="index.php">首頁</a>
            <a class="<?= $active === 'register' ? 'active' : '' ?>" href="register.php">註冊</a>
            <a class="<?= $active === 'login' ? 'active' : '' ?>" href="login.php">登入</a>
            <a class="<?= $active === 'memo' ? 'active' : '' ?>" href="memo.php">生活備忘</a>
            <a class="<?= $active === 'logs' ? 'active' : '' ?>" href="logs.php">登入紀錄</a>
        </nav>
        <div class="user-chip">
            <?php if ($user): ?>
                <span>Hi, <?= e($user['nickname']) ?></span>
                <a href="logout.php">登出</a>
            <?php else: ?>
                <span>尚未登入</span>
            <?php endif; ?>
        </div>
    </header>

    <main class="content">
        <?php if ($flash): ?>
            <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
    <?php
}

function render_footer(): void
{
    ?>
    </main>
</div>
</body>
</html>
    <?php
}

function login_attempt(string $account, ?int $userId, bool $success, string $message): void
{
    $pdo = db();
    $stmt = $pdo->prepare('INSERT INTO dblog (user_account, user_id, login_time, is_success, ip_address, message) VALUES (:user_account, :user_id, NOW(), :is_success, :ip_address, :message)');
    $stmt->execute([
        'user_account' => $account,
        'user_id' => $userId,
        'is_success' => $success ? 1 : 0,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'message' => $message,
    ]);
}

function ensure_upload_dirs(): void
{
    $paths = [__DIR__ . DIRECTORY_SEPARATOR . 'uploads', __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'thumbs'];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}

function image_create_from_file(string $path, string $mime)
{
    return match ($mime) {
        'image/jpeg' => imagecreatefromjpeg($path),
        'image/png' => imagecreatefrompng($path),
        'image/gif' => imagecreatefromgif($path),
        'image/webp' => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : false,
        default => false,
    };
}

function save_thumbnail(string $sourcePath, string $thumbPath, int $maxWidth = 480, int $maxHeight = 360): bool
{
    $info = getimagesize($sourcePath);
    if ($info === false) {
        return false;
    }

    $width = (int) ($info[0] ?? 0);
    $height = (int) ($info[1] ?? 0);
    $mime = (string) ($info['mime'] ?? '');
    if ($width <= 0 || $height <= 0 || $mime === '') {
        return false;
    }

    $source = image_create_from_file($sourcePath, $mime);
    if (!$source) {
        return false;
    }

    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
    $thumbWidth = max(1, (int) round($width * $ratio));
    $thumbHeight = max(1, (int) round($height * $ratio));

    $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
    $white = imagecolorallocate($thumb, 255, 255, 255);
    imagefill($thumb, 0, 0, $white);
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
    imagejpeg($thumb, $thumbPath, 88);

    imagedestroy($source);
    imagedestroy($thumb);

    return true;
}

function upload_memo_image(array $file): array
{
    ensure_upload_dirs();

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('請上傳一張圖片。');
    }

    $tmpPath = $file['tmp_name'];
    $mime = mime_content_type($tmpPath) ?: '';
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException('只支援 JPG、PNG、GIF、WEBP 圖片。');
    }

    $extension = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        default => 'jpg',
    };

    $fileBase = uniqid('memo_', true);
    $imageName = $fileBase . '.' . $extension;
    $thumbName = $fileBase . '_thumb.jpg';

    $imagePath = 'uploads/' . $imageName;
    $thumbPath = 'uploads/thumbs/' . $thumbName;

    $absoluteImage = __DIR__ . DIRECTORY_SEPARATOR . $imagePath;
    $absoluteThumb = __DIR__ . DIRECTORY_SEPARATOR . $thumbPath;

    if (!move_uploaded_file($tmpPath, $absoluteImage)) {
        throw new RuntimeException('圖片上傳失敗。');
    }

    if (!save_thumbnail($absoluteImage, $absoluteThumb)) {
        @unlink($absoluteImage);
        throw new RuntimeException('縮圖建立失敗，請確認伺服器已啟用 GD 擴充。');
    }

    return [$imagePath, $thumbPath];
}

function delete_file_if_exists(?string $relativePath): void
{
    if (!$relativePath) {
        return;
    }

    $absolute = __DIR__ . DIRECTORY_SEPARATOR . $relativePath;
    if (is_file($absolute)) {
        @unlink($absolute);
    }
}

function ensure_thumbnail_for_memo(PDO $pdo, array $memo): string
{
    $imagePath = trim((string) ($memo['image_path'] ?? ''));
    $thumbPath = trim((string) ($memo['thumbnail_path'] ?? ''));

    if ($imagePath === '') {
        return '';
    }

    $absoluteImage = __DIR__ . DIRECTORY_SEPARATOR . $imagePath;
    if (!is_file($absoluteImage)) {
        return $thumbPath !== '' ? $thumbPath : $imagePath;
    }

    if ($thumbPath === '') {
        $basename = pathinfo($imagePath, PATHINFO_FILENAME);
        $thumbPath = 'uploads/thumbs/' . $basename . '_thumb.jpg';
    }

    $absoluteThumb = __DIR__ . DIRECTORY_SEPARATOR . $thumbPath;
    if (!is_file($absoluteThumb)) {
        ensure_upload_dirs();
        if (!save_thumbnail($absoluteImage, $absoluteThumb)) {
            return $imagePath;
        }
    }

    if (($memo['thumbnail_path'] ?? '') !== $thumbPath && !empty($memo['memo_id'])) {
        $stmt = $pdo->prepare('UPDATE dbmemo SET thumbnail_path = :thumbnail_path WHERE memo_id = :memo_id');
        $stmt->execute([
            'thumbnail_path' => $thumbPath,
            'memo_id' => (int) $memo['memo_id'],
        ]);
    }

    return $thumbPath;
}