<?php
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $error = 'Yeni şifreler eşleşmiyor!';
    } else {
        $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $user = $stmt->fetch();

        if (password_verify($current_password, $user['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $stmt->execute([$new_hash, $_SESSION['admin_id']]);
            $message = 'Şifreniz başarıyla değiştirildi!';
        } else {
            $error = 'Mevcut şifreniz yanlış!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ayarlar - Slot Eser</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>⚙️ Ayarlar</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="site-card">
            <div class="site-info" style="width:100%">
                <h3>🔐 Şifre Değiştir</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Mevcut Şifre</label>
                        <input type="password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label>Yeni Şifre</label>
                        <input type="password" name="new_password" required minlength="6">
                    </div>

                    <div class="form-group">
                        <label>Yeni Şifre (Tekrar)</label>
                        <input type="password" name="confirm_password" required minlength="6">
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Şifreyi Değiştir</button>
                </form>
            </div>
        </div>

        <div class="site-card" style="margin-top:20px">
            <div class="site-info" style="width:100%">
                <h3>ℹ️ Sistem Bilgileri</h3>
                <p>
                    <strong>PHP Versiyonu:</strong> <?= phpversion() ?><br>
                    <strong>Kullanıcı Adı:</strong> <?= htmlspecialchars($_SESSION['admin_username']) ?><br>
                    <strong>Veritabanı:</strong> MySQL<br>
                    <strong>Upload Dizini:</strong> <?= UPLOAD_DIR ?><br>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
