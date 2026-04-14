[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-22041afd0340ce965d47ae6ef1cefeee28c7c493a6346c4f15d667ab976d596c.svg)](https://classroom.github.com/a/VOLNfwbe)
# 作業 5 資料庫基礎存取

## 繳交說明
1. 分組名稱請依照分組表上進行更名，甲班為 A01, A02, .. A12 乙班則為 B01, B02, .. B11。更名若有問題，請找老師協助！
2. 分組作業，不開 PR, 所以也不開branch。
3. 繳交期限  4/20
4. 繳交後可以找時間整組找老師進行demo，請組員理解你們的程式碼，老師會個別問問題。
   
## 作業說明
自行設計題目，但須具備下列功能: 
1. 三個資料表，一個用來存放註冊資料，一個用來存放 log 資料，一個用來存放圖文備忘資料。
   資料表命名分別為 dbusers, dblog 及 dememo 。完成後請將資料表匯出，填入到資料夾中。
2. 具備註冊功能，註冊資料包含
   a. 帳號
   b. 暱稱
   c. 密碼
   d. 性別
   e. 興趣
   ...
3. 具備登入功能，需註冊後才能登入
4. 任何人登入時，紀錄登入者帳號，日期時間以及是否登入成功
5. 登入後可以新增圖文備忘，至少包含
   a. 新增者(使用者id)
   b. 多行文字
   c. 上傳一張圖片，進行縮圖後存放
   d. ...
6. 圖文備忘功能具備 新增、刪除、修改、列出
7. 登入資料可以被瀏覽

## 自行設計的內容說明(同學自填)

本作業的設計題目為「圖文備忘錄系統」，資料結構分成三部分：

| 資料表 | 用途 | 主要欄位 |
|------|------|------|
| dbusers | 註冊資料 | account、nickname、password_hash、gender、hobbies |
| dblog | 登入紀錄 | user_account、user_id、login_time、is_success、ip_address |
| dbmemo | 圖文備忘 | creator_id、title、content、image_path、thumbnail_path |

功能對應如下：

1. 註冊功能：使用 dbusers 儲存帳號、暱稱、密碼、性別與興趣。
2. 登入功能：登入時寫入 dblog，包含帳號、時間與成功或失敗結果。
3. 圖文備忘：使用 dbmemo 儲存文字內容與圖片路徑，並保留縮圖路徑。
4. CRUD：dbmemo 支援新增、刪除、修改與列表查詢。
5. 瀏覽登入資料：可直接查詢 dblog 觀看登入紀錄。

補充說明：作業說明中提到的 dememo，這裡以匯出檔名稱 dbmemo 呈現，內容是同一張圖文備忘資料表。

1. 將專案放在 Laragon 的 `www` 資料夾後，直接開啟 `index.php`。
2. 先到 `register.php` 註冊帳號，再到 `login.php` 登入。
3. 登入後可到 `memo.php` 新增、修改、刪除圖文備忘。
4. 到 `logs.php` 可瀏覽所有登入紀錄。
5. 系統會自動建立資料庫與資料表，也會在首次上傳圖片時建立 `uploads` 與縮圖資料夾。


這個網站主題是「日常生活分享記錄」，希望使用者像在整理日記一樣，把每天的照片與文字備份下來。頁面設計以卡片式版面、漸層背景與縮圖瀏覽為主，讓功能看起來像一個簡潔的生活分享平台，而不是只有資料庫練習頁面。