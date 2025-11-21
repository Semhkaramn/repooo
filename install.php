<?php
/**
 * KURULUM DOSYASI
 * Bu dosyayı sadece bir kez çalıştırın!
 * Çalıştırdıktan sonra silin veya adını değiştirin!
 */

require_once 'config.php';

try {
    // Sites tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS sites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        logo VARCHAR(255) NOT NULL,
        url TEXT NOT NULL,
        color VARCHAR(7) DEFAULT '#ff0000',
        bonus_title VARCHAR(255),
        bonus_description TEXT,
        category ENUM('vip', 'guvenilir') DEFAULT 'vip',
        show_in_marquee TINYINT(1) DEFAULT 0,
        likes INT DEFAULT 0,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Fix Banners tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS fix_banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        position ENUM('left', 'right') NOT NULL,
        site_id INT NOT NULL,
        banner_image VARCHAR(255) NOT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Admin Users tablosu
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Default admin user (kullanıcı adı: admin, şifre: admin123)
    $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->execute(['admin', $defaultPassword]);

    echo "✅ Veritabanı başarıyla kuruldu!<br>";
    echo "📌 Admin Giriş Bilgileri:<br>";
    echo "Kullanıcı Adı: <strong>admin</strong><br>";
    echo "Şifre: <strong>admin123</strong><br><br>";
    echo "⚠️ <strong>GÜVENLİK UYARISI:</strong> Giriş yaptıktan sonra şifrenizi değiştirin!<br>";
    echo "⚠️ Bu dosyayı hemen silin veya adını değiştirin!<br>";

} catch(PDOException $e) {
    die("Kurulum hatası: " . $e->getMessage());
}
?>
