<?php
require 'db_log_in.php';

if (empty($_SESSION['is_admin'])) {
    exit('無權限存取此頁面');
}

try {
    $stmt = $pdo->query('SELECT id, user_id, name, login_time, login_success FROM dblog ORDER BY login_time DESC, id DESC');
    $records = $stmt->fetchAll();
} catch (PDOException $e) {
    exit('讀取資料失敗：' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>所有登入資料</title>
    <style>
        body {
            font-family: "Microsoft JhengHei", sans-serif;
            margin: 24px;
            background: #f5f7fb;
            color: #1f2937;
        }

        .wrap {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        h1 {
            margin-top: 0;
            font-size: 28px;
        }

        .meta {
            margin-bottom: 16px;
            color: #4b5563;
        }

        .actions {
            margin-bottom: 16px;
        }

        .logout-btn {
            display: inline-block;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 6px;
            background: #ef4444;
            color: #ffffff;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #eef2ff;
        }

        .empty {
            padding: 12px;
            background: #fff7ed;
            color: #9a3412;
            border: 1px solid #fed7aa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>登入日誌</h1>
        <div class="meta">管理員：<?php echo htmlspecialchars($_SESSION['admin_username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        <div class="actions">
            <a class="logout-btn" href="admin_logout.php">管理員登出</a>
        </div>

        <?php if (empty($records)): ?>
            <div class="empty">目前沒有登入資料。</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>使用者ID(user_id)</th>
                        <th>帳號(name)</th>
                        <th>登入時間(login_time)</th>
                        <th>登入成功(login_success)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars((string) $row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($row['user_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) $row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) $row['login_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) ($row['login_success'] ? '是' : '否'), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
