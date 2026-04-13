# 資料庫基礎教學

## 1. 資料庫的基本概念

你可以把資料庫想像成一個 Excel 檔案：

- **Database（資料庫）**：整個 Excel 檔案（例如：my_shop）
- **Table（資料表）**：檔案中的分頁（例如：users, products）
- **Column（欄位）**：表格的標題（例如：id, username, password）
- **Row（資料列）**：每一筆實際的資料

## 2. 使用 Laragon 管理資料庫 (PHPMyadmin)

1. 在工具中打開 PHPMyadmin
2. 新增資料庫，命名為 test_db

## 3. 核心 SQL 語法 (CRUD)

要操作資料庫，我們必須學習 SQL 語言。最核心的是 CRUD 四大操作：

| 操作 | SQL 動作 | 說明 |
|------|---------|------|
| Create | INSERT | 新增一筆資料 |
| Read | SELECT | 讀取/搜尋資料 |
| Update | UPDATE | 修改現有資料 |
| Delete | DELETE | 刪除資料 |

## 4. 實戰練習：建立一張員工表

在 phpmyadmin 的「查詢 (Query)」分頁輸入以下指令並執行：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, email) VALUES ('Alex', 'alex@example.com');
```

## 5. 使用 PDO 連接資料庫

在現代 PHP 開發中，我們使用 **PDO (PHP Data Objects)**。它比舊式的 mysqli 更安全，能有效防止 SQL 注入攻擊。

請在 www 資料夾下建立 `db_test.php`，並填入以下內容（請確保你已建立 test_db）：

```php
<?php
$host = '127.0.0.1';
$db   = 'test_db';
$user = 'root';
$pass = ''; // Laragon 預設密碼為空
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // 建立連接
    $pdo = new PDO($dsn, $user, $pass);
    echo "✅ 資料庫連線成功！";

    // 嘗試抓取資料
    $stmt = $pdo->query("SELECT username FROM users");
    while ($row = $stmt->fetch()) {
        echo "<p>使用者姓名：" . $row['username'] . "</p>";
    }

} catch (PDOException $e) {
    echo "❌ 連線失敗: " . $e->getMessage();
}
?>
```

## 6. 為什麼要學這個？

### 安全性
PDO 支援「**預處理語句 (Prepared Statements)**」，這是保護網站不被駭客輕易入侵的防護牆。

### 彈性
未來如果你想從 MySQL 換成 PostgreSQL，PDO 的寫法幾乎不需要大改。