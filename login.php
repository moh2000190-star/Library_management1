<?php
require 'db.php';
session_start();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) $errors[] = "أدخل اسم المستخدم وكلمة المرور";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, password_hash, role FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: search.php');
            exit;
        } else {
            $errors[] = "بيانات الدخول خاطئة";
        }
    }
}
include 'header.php';
?>
<h3>تسجيل الدخول</h3>
<div class="form">
  <?php if(isset($_GET['registered'])): ?><div class="alert">تم إنشاء الحساب. يرجى تسجيل الدخول.</div><?php endif; ?>
  <?php if($errors): foreach($errors as $e): ?><div class="alert"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>
  <form method="post">
    <label>اسم المستخدم أو البريد</label>
    <input name="username" required>
    <label>كلمة المرور</label>
    <input name="password" type="password" required>
    <button class="btn" type="submit">دخول</button>
  </form>
</div>
<?php
echo '</main></body></html>';
