<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header('Location: login.php'); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $copies = max(1, intval($_POST['copies'] ?? 1));
    $desc = trim($_POST['description'] ?? '');
    if (!$title) $errors[] = "العنوان مطلوب";
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, isbn, copies, available, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $isbn, $copies, $copies, $desc]);
        header('Location: search.php?msg=book_added');
        exit;
    }
}
include 'header.php';
?>
<h3>إضافة كتاب جديد</h3>
<div class="form">
  <?php if($errors): foreach($errors as $e): ?><div class="alert"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>
  <form method="post">
    <label>العنوان</label>
    <input name="title" required>
    <label>المؤلف</label>
    <input name="author">
    <label>ISBN</label>
    <input name="isbn">
    <label>عدد النسخ</label>
    <input name="copies" type="number" value="1" min="1">
    <label>وصف</label>
    <textarea name="description"></textarea>
    <button class="btn" type="submit">إضافة الكتاب</button>
  </form>
</div>
<?php
echo '</main></body></html>';
