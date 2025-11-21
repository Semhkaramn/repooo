<?php
// Hostinger Database Configuration
define('DB_HOST', 'localhost'); // Hostinger'da genelde localhost
define('DB_USER', 'u944078781_semhkaramn'); // Hostinger'dan alacağınız kullanıcı adı
define('DB_PASS', 'Abuzittin74.'); // Hostinger'dan alacağınız şifre
define('DB_NAME', 'u944078781_sago'); // Veritabanı adı

// Database Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Admin Session
session_start();

// Upload Directory
define('UPLOAD_DIR', __DIR__ . '/uploads/');
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Site URL
define('SITE_URL', 'https://deeppink-baboon-608965.hostingersite.com'); // Sitenizin URL'i
?>
