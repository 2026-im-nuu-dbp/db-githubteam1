<?php
require 'db_log_in.php';

unset($_SESSION['is_admin'], $_SESSION['admin_username']);
session_regenerate_id(true);

header('Location: log_in.html');
exit;
?>
