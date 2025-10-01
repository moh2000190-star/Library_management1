<?php
require 'db.php';
session_start();
if (!isset($_SESSION['users_id'])) { header('Location: login.php'); exit; }
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role']==='admin');

if ($isAdmin) {
    $stmt = $pdo->query("SELECT b.*, u.username, bk.title FROM borrows b JOIN users u ON b.user_id = u.id JOIN books bk ON b.book_id = bk.id ORDER BY b.created_at DESC");
    $rows = $stmt->fetchAll();
} else {
    $uid = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT b.*, bk.title FROM borrows b JOIN books bk ON b.book_id = bk.id WHERE b.user_id = ? ORDER BY b.created_at DESC");
    $stmt->execute([$uid]);
    $rows = $stmt->fetchAll();
}

include 'header.php';
?>
<h3>سجلات الاستعارة</h3>
<?php if(isset($_GET['msg'])): ?><div class="alert"><?=htmlspecialchars($_GET['msg'])?></div><?php endif; ?>
<table class="table">
<tr><th>الكتاب</th><?php if($isAdmin) echo '<th>المستخدم</th>'; ?><th>نوع</th><th>تاريخ</th><th>الموعد</th><th>الإرجاع</th><th>الحالة</th><th>إجراءات</th></tr>
<?php foreach($rows as $r): ?>
<tr>
  <td><?=htmlspecialchars($r['title'])?></td>
  <?php if($isAdmin) echo '<td>'.htmlspecialchars($r['username']).'</td>'; ?>
  <td><?=htmlspecialchars($r['status'])?></td>
  <td><?=htmlspecialchars($r['borrow_date'])?></td>
  <td><?=htmlspecialchars($r['due_date'])?></td>
  <td><?=htmlspecialchars($r['return_date'] ?? '-')?></td>
  <td><?=htmlspecialchars($r['status'])?></td>
  <td>
    <?php if($isAdmin && $r['status'] !== 'returned'): ?>
      <a class="btn" href="return_book.php?id=<?=$r['id']?>">وضع كمُرجع</a>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</table>
<?php
echo '</main></body></html>';
