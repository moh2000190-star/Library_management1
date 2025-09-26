<?php
require 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$book_id = intval($_GET['book_id'] ?? 0);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id']);
    $stmt = $pdo->prepare("SELECT available FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    if (!$book) $errors[] = "الكتاب غير موجود";
    elseif ($book['available'] <= 0) $errors[] = "لا توجد نسخ متاحة للاستعارة";
    else {
        $borrow_date = date('Y-m-d');
        $due_date = date('Y-m-d', strtotime('+14 days'));
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO borrows (user_id, book_id, borrow_date, due_date, status) VALUES (?, ?, ?, ?, 'borrowed')");
        $stmt->execute([$user_id, $book_id, $borrow_date, $due_date]);
        $stmt = $pdo->prepare("UPDATE books SET available = available - 1 WHERE id = ?");
        $stmt->execute([$book_id]);
        $pdo->commit();
        header('Location: borrow_records.php?msg=borrowed');
        exit;
    }
}
include 'header.php';
if ($book_id) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
} else {
    $book = null;
}
?>
<h3>استعارة كتاب</h3>
<div class="form">
  <?php if($errors): foreach($errors as $e): ?><div class="alert"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>
  <?php if(!$book): ?>
    <div>لم يتم اختيار كتاب. انتقل إلى صفحة البحث لاختيار كتاب.</div>
  <?php else: ?>
    <p><strong>العنوان:</strong> <?=htmlspecialchars($book['title'])?></p>
    <p><strong>المتوفر:</strong> <?=intval($book['available'])?> نسخ</p>
    <form method="post">
      <input type="hidden" name="book_id" value="<?=$book['id']?>">
      <button class="btn" type="submit">تأكيد الاستعارة (مدة 14 يوم)</button>
    </form>
  <?php endif; ?>
</div>
<?php
echo '</main></body></html>';
