<?php
// db.php — إعداد مبدئي مناسب لـ XAMPP
$host = '127.0.0.1';
$db   = 'library_db';
$user = 'root';
$pass = ''; // XAMPP الافتراضي غالباً لا يحتوي كلمة مرور للـ root
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
