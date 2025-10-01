<?php
// delete_book.php
require 'db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// تحقق صلاحية الأدمن
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

// تأكد الطلب POST واستقبال id صحيح
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: search.php');
    exit;
}
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: search.php?msg=invalid_id');
    exit;
}

try {
    $pdo->beginTransaction();

    // حذف سجلات borrows المرتبطة أولاً لتجنب قيود المفتاح الأجنبي (إن وجدت)
    $stmt = $pdo->prepare("DELETE FROM borrows WHERE book_id = ?");
    $stmt->execute([$id]);

    // حذف الكتاب نفسه
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$id]);

    $deleted = $stmt->rowCount();
    $pdo->commit();

    header('Location: search.php?msg=' . ($deleted ? 'book_deleted' : 'not_found'));
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Delete book error: " . $e->getMessage());
    header('Location: search.php?msg=delete_failed');
    exit;
}
