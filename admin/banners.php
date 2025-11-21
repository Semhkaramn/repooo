<?php
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $id = $_POST['id'] ?? null;
        $position = $_POST['position'] ?? 'left';
        $site_id = $_POST['site_id'] ?? 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Banner upload
        $banner = '';
        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['banner']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $banner = 'banner_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['banner']['tmp_name'], UPLOAD_DIR . $banner);
            }
        } elseif ($action === 'edit' && $id) {
            $stmt = $pdo->prepare("SELECT banner_image FROM fix_banners WHERE id = ?");
            $stmt->execute([$id]);
            $banner = $stmt->fetchColumn();
        }

        if ($action === 'add') {
            // Deactivate other banners in the same position
            $stmt = $pdo->prepare("UPDATE fix_banners SET is_active = 0 WHERE position = ?");
            $stmt->execute([$position]);

            $stmt = $pdo->prepare("INSERT INTO fix_banners (position, site_id, banner_image, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute([$position, $site_id, $banner, $is_active]);
            $message = 'Banner başarıyla eklendi!';
        } else {
            if ($is_active) {
                $stmt = $pdo->prepare("UPDATE fix_banners SET is_active = 0 WHERE position = ? AND id != ?");
                $stmt->execute([$position, $id]);
            }

            $stmt = $pdo->prepare("UPDATE fix_banners SET position = ?, site_id = ?, banner_image = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$position, $site_id, $banner, $is_active, $id]);
            $message = 'Banner başarıyla güncellendi!';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM fix_banners WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Banner başarıyla silindi!';
    }
}

// Get all banners
$banners = $pdo->query("
    SELECT b.*, s.name as site_name, s.url as site_url
    FROM fix_banners b
    LEFT JOIN sites s ON b.site_id = s.id
    ORDER BY b.position, b.created_at DESC
")->fetchAll();

// Get all sites for dropdown
$sites = $pdo->query("SELECT id, name FROM sites ORDER BY name")->fetchAll();

// Get banner for editing
$editBanner = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM fix_banners WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editBanner = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Yönetimi - Slot Eser</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>🖼️ Yan Banner Yönetimi</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="site-card">
            <div class="site-info" style="width:100%">
                <h3><?= $editBanner ? '✏️ Banner Düzenle' : '➕ Yeni Banner Ekle' ?></h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?= $editBanner ? 'edit' : 'add' ?>">
                    <?php if ($editBanner): ?>
                        <input type="hidden" name="id" value="<?= $editBanner['id'] ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Pozisyon *</label>
                            <select name="position" required>
                                <option value="left" <?= ($editBanner['position'] ?? '') === 'left' ? 'selected' : '' ?>>Sol Taraf</option>
                                <option value="right" <?= ($editBanner['position'] ?? '') === 'right' ? 'selected' : '' ?>>Sağ Taraf</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Site Seçin *</label>
                            <select name="site_id" required>
                                <option value="">Bir site seçin...</option>
                                <?php foreach ($sites as $site): ?>
                                    <option value="<?= $site['id'] ?>" <?= ($editBanner['site_id'] ?? 0) == $site['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($site['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Banner Resmi * (Önerilen: 300x600 piksel)</label>
                        <div class="file-upload">
                            <input type="file" name="banner" accept="image/*" <?= !$editBanner ? 'required' : '' ?>>
                            <div class="file-upload-label">
                                📁 Banner Seç (.jpg, .png, .gif, .webp)
                            </div>
                        </div>
                        <?php if ($editBanner && $editBanner['banner_image']): ?>
                            <div style="margin-top:10px">
                                <img src="../uploads/<?= htmlspecialchars($editBanner['banner_image']) ?>" alt="Current Banner" style="max-width:200px">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_active" value="1" <?= ($editBanner['is_active'] ?? 1) ? 'checked' : '' ?>>
                            Aktif (Bu pozisyonda sadece 1 banner aktif olabilir)
                        </label>
                    </div>

                    <div style="display:flex;gap:10px">
                        <button type="submit" class="btn btn-primary">
                            <?= $editBanner ? '💾 Güncelle' : '➕ Ekle' ?>
                        </button>
                        <?php if ($editBanner): ?>
                            <a href="banners.php" class="btn btn-warning">❌ İptal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Banners List -->
        <h2 style="margin-top:40px">Mevcut Bannerlar</h2>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:20px">
            <?php foreach ($banners as $banner): ?>
                <div class="site-card" style="flex-direction:column;align-items:flex-start">
                    <img src="../uploads/<?= htmlspecialchars($banner['banner_image']) ?>" alt="Banner" style="width:100%;max-height:300px;object-fit:contain;border-radius:5px">
                    <div class="site-info" style="width:100%">
                        <h3>
                            <?= $banner['position'] === 'left' ? '⬅️ Sol Taraf' : '➡️ Sağ Taraf' ?>
                            <?= $banner['is_active'] ? '<span class="badge" style="background:#28a745;color:white">AKTİF</span>' : '<span class="badge" style="background:#ccc;color:#333">PASİF</span>' ?>
                        </h3>
                        <p>
                            <strong>Site:</strong> <?= htmlspecialchars($banner['site_name']) ?><br>
                            <strong>URL:</strong> <?= htmlspecialchars($banner['site_url']) ?><br>
                            <strong>Eklenme:</strong> <?= date('d.m.Y H:i', strtotime($banner['created_at'])) ?>
                        </p>
                    </div>
                    <div class="site-actions">
                        <a href="?edit=<?= $banner['id'] ?>" class="btn btn-info">✏️ Düzenle</a>
                        <form method="POST" onsubmit="return confirm('Bu banneri silmek istediğinizden emin misiniz?')" style="margin:0">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $banner['id'] ?>">
                            <button type="submit" class="btn btn-danger">🗑️ Sil</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($banners)): ?>
            <p style="text-align:center;padding:40px;color:#999">Henüz banner eklenmemiş.</p>
        <?php endif; ?>
    </div>
</body>
</html>
