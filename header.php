<?php
// header.php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = ($isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>
<!doctype html>
<html lang="ar">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>نظام إدارة المكتبة</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="topbar">
  <div class="brand">مكتبة الجامعة</div>
  <nav class="nav">
    <a href="search.php">بحث عن كتاب</a>
    <?php if($isLoggedIn): ?>
      <a href="borrow.php">استعارة كتب</a>
      <a href="reserve.php">حجز كتاب</a>
      <a href="borrow_records.php">سجلات الاستعارة</a>
      <?php if($isAdmin): ?>
        <a href="add_book.php">إضافة كتاب</a>
      <?php endif; ?>
      <a href="logout.php">تسجيل الخروج</a>
    <?php else: ?>
      <a href="login.php">تسجيل الدخول</a>
      <a href="register.php">إنشاء حساب</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
</main>
<footer class="footer">© <?=date('Y')?> نظام إدارة المكتبة</footer>
</body>
</html>
