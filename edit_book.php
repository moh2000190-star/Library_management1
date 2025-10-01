<?php
// edit_book.php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// صلاحية الأدمن
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

$errors = [];
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: search.php?msg=invalid_id');
    exit;
}

// جلب بيانات الكتاب للعرض في النموذج
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();
if (!$book) {
    header('Location: search.php?msg=not_found');
    exit;
}

// معالجة التحديث
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn = trim($_POST['isbn'] ?? '');
    $copies = max(1, intval($_POST['copies'] ?? 1));
    $description = trim($_POST['description'] ?? '');

    if ($title === '') $errors[] = "العنوان مطلوب";

    if (empty($errors)) {
        // حساب الفرق لتعديل available بشكل منطقي
        $oldCopies = intval($book['copies']);
        $oldAvailable = intval($book['available']);
        $diff = $copies - $oldCopies;
        $newAvailable = $oldAvailable + $diff;
        if ($newAvailable < 0) $newAvailable = 0;

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, isbn = ?, copies = ?, available = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $author, $isbn, $copies, $newAvailable, $description, $id]);
            $pdo->commit();
            header("Location: search.php?msg=book_updated");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "فشل التحديث، حاول لاحقاً";
        }
    }
}
?>
<?php include 'header.php'; ?>
<h3>تعديل كتاب</h3>
<div class="form">
  <?php if($errors): foreach($errors as $e): ?><div class="alert"><?=htmlspecialchars($e)?></div><?php endforeach; endif; ?>
  <form method="post">
    <label>العنوان</label>
    <input name="title" required value="<?=htmlspecialchars($book['title'])?>">
    <label>المؤلف</label>
    <input name="author" value="<?=htmlspecialchars($book['author'])?>">
    <label>ISBN</label>
    <input name="isbn" value="<?=htmlspecialchars($book['isbn'])?>">
    <label>عدد النسخ</label>
    <input name="copies" type="number" min="1" value="<?=intval($book['copies'])?>">
    <label>الوصف</label>
    <textarea name="description"><?=htmlspecialchars($book['description'])?></textarea>
    <button class="btn" type="submit">حفظ التغييرات</button>
    <a class="btn" href="search.php" style="background:#3498db;margin-left:8px">إلغاء</a>
  </form>
</div>
<?php echo '</main></body></html>'; ?>
