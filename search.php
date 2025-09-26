<?php
require 'db.php';
include 'header.php';
$term = trim($_GET['q'] ?? '');
$books = [];
if ($term) {
    $like = "%$term%";
    $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? ORDER BY created_at DESC");
    $stmt->execute([$like, $like, $like]);
    $books = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 50");
    $books = $stmt->fetchAll();
}

?>
<h3>بحث عن كتاب</h3>
<form method="get" class="form">
  <input name="q" placeholder="اكتب عنوان، مؤلف أو ISBN" value="<?=htmlspecialchars($term)?>">
  <button class="btn" type="submit">بحث</button>
</form>



<table class="table">
  <tr><th>العنوان</th><th>المؤلف</th><th>الرمز ISBN</th><th>المتوفر</th><th>إجراءات</th></tr>
  <?php foreach($books as $b): ?>
  <tr>
    <td><?=htmlspecialchars($b['title'])?></td>
    <td><?=htmlspecialchars($b['author'])?></td>
    <td><?=htmlspecialchars($b['isbn'])?></td>
    <td><?=intval($b['available'])?> / <?=intval($b['copies'])?></td>
    <td>
      <?php if(isset($_SESSION['user_id'])): ?>
        <?php if($b['available']>0): ?>
          <a class="btn" href="borrow.php?book_id=<?=$b['id']?>">استعارة</a>
        <?php else: ?>
          <a class="btn" href="reserve.php?book_id=<?=$b['id']?>">حجز</a>
        <?php endif; ?>
        <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
          <a class="btn" href="edit_book.php?id=<?=$b['id']?>">تعديل</a>
          <a class="btn" href="delete_book.php?id=<?=$b['id']?>" onclick="return confirm('هل تريد الحذف؟')">حذف</a>
        <?php endif; ?>
      <?php else: ?>
        <a class="btn" href="login.php">تسجيل الدخول للاستعر</a>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<?php
echo '</main></body></html>';
