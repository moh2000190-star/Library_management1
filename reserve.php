<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = $_SESSION['user_id'];
$book_id = intval($_GET['book_id'] ?? 0);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id']);
    $stmt = $pdo->prepare("SELECT available FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    if (!$book) $errors[] = "الكتاب غير موجود";
    else {
        $reserve_date = date('Y-m-d');
        $pdo->beginTransaction();
        // نضع السجل كـ reserved وتقلل النسخة المتاحة مؤقتاً لمنع حجز متكرر
        $stmt = $pdo->prepare("INSERT INTO borrows (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'reserved')");
        $stmt->execute([$user_id, $book_id, $reserve_date, $reserve_date]);
        if ($book['available'] > 0) {
            $stmt = $pdo->prepare("UPDATE books SET available = available - 1 WHERE id = ?");
            $stmt->execute([$book_id]);
        }
        $pdo->commit();
        header('Location: borrow_records.php?msg=reserved');
        exit;
    }
}
include 'header.php';
if ($book_id) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
} else { $book = null; }
?>
<h3>حجز كتاب</h3>
<div class="form">
  <?php if($errors): foreach($errors as $e): ?><div class="alert"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>
  <?php if(!$book): ?>
    <div>لم يتم اختيار كتاب للحجز.</div>
  <?php else: ?>
    <p><strong>العنوان:</strong> <?=htmlspecialchars($book['title'])?></p>
    <form method="post">
      <input type="hidden" name="book_id" value="<?=$book['id']?>">
      <button class="btn" type="submit">تأكيد الحجز</button>
    </form>
  <?php endif; ?>
</div>
<?php
echo '</main></body></html>';
