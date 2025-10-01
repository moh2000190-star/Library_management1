<?php
require 'db.php';
session_start();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$full_name || !$email || !$password) $errors[] = "جميع الحقول مطلوبة";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "اسم المستخدم أو البريد مستخدم بالفعل";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $full_name, $email, $hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
include 'header.php';
?>
<h3>إنشاء حساب جديد</h3>
<div class="form">
  <?php if($errors): foreach($errors as $e): ?><div class="alert"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>
  <form method="post">
    <label>اسم المستخدم</label>
    <input name="username" required>
    <label>الاسم الكامل</label>
    <input name="full_name" required>
    <label>البريد الإلكتروني</label>
    <input name="email" type="email" required>
    <label>كلمة المرور</label>
    <input name="password" type="password" required>
    <button class="btn" type="submit">إنشاء الحساب</button>
  </form>
</div>
<?php
echo '</main></body></html>';
