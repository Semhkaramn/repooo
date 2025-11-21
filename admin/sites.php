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
        $name = $_POST['name'] ?? '';
        $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $name)));
        $url = $_POST['url'] ?? '';
        $color = $_POST['color'] ?? '#ff0000';
        $bonus_title = $_POST['bonus_title'] ?? '';
        $bonus_description = $_POST['bonus_description'] ?? '';
        $category = $_POST['category'] ?? 'vip';
        $show_in_marquee = isset($_POST['show_in_marquee']) ? 1 : 0;
        $display_order = $_POST['display_order'] ?? 0;

        // Logo upload
        $logo = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['logo']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $logo = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['logo']['tmp_name'], UPLOAD_DIR . $logo);
            }
        } elseif ($action === 'edit' && $id) {
            // Keep existing logo
            $stmt = $pdo->prepare("SELECT logo FROM sites WHERE id = ?");
            $stmt->execute([$id]);
            $logo = $stmt->fetchColumn();
        }

        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO sites (name, slug, logo, url, color, bonus_title, bonus_description, category, show_in_marquee, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $logo, $url, $color, $bonus_title, $bonus_description, $category, $show_in_marquee, $display_order]);
            $message = 'Site başarıyla eklendi!';
        } else {
            $stmt = $pdo->prepare("UPDATE sites SET name = ?, slug = ?, logo = ?, url = ?, color = ?, bonus_title = ?, bonus_description = ?, category = ?, show_in_marquee = ?, display_order = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $logo, $url, $color, $bonus_title, $bonus_description, $category, $show_in_marquee, $display_order, $id]);
            $message = 'Site başarıyla güncellendi!';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM sites WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Site başarıyla silindi!';
    }
}

// Get all sites
$sites = $pdo->query("SELECT * FROM sites ORDER BY display_order ASC, created_at DESC")->fetchAll();

// Get site for editing
$editSite = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM sites WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editSite = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Yönetimi - Slot Eser</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>🎰 Site Yönetimi</h1>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="site-card">
            <div class="site-info" style="width:100%">
                <h3><?= $editSite ? '✏️ Site Düzenle' : '➕ Yeni Site Ekle' ?></h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?= $editSite ? 'edit' : 'add' ?>">
                    <?php if ($editSite): ?>
                        <input type="hidden" name="id" value="<?= $editSite['id'] ?>">
                    <?php endif; ?>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Site Adı *</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($editSite['name'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Site URL *</label>
                            <input type="url" name="url" value="<?= htmlspecialchars($editSite['url'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Renk Kodu</label>
                            <input type="color" name="color" value="<?= htmlspecialchars($editSite['color'] ?? '#ff0000') ?>">
                        </div>

                        <div class="form-group">
                            <label>Sıralama</label>
                            <input type="number" name="display_order" value="<?= htmlspecialchars($editSite['display_order'] ?? 0) ?>" min="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Bonus Başlığı</label>
                        <input type="text" name="bonus_title" value="<?= htmlspecialchars($editSite['bonus_title'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Bonus Açıklaması</label>
                        <textarea name="bonus_description"><?= htmlspecialchars($editSite['bonus_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label>Logo *</label>
                            <div class="file-upload">
                                <input type="file" name="logo" accept="image/*" <?= !$editSite ? 'required' : '' ?>>
                                <div class="file-upload-label">
                                    📁 Logo Seç (.jpg, .png, .gif, .webp)
                                </div>
                            </div>
                            <?php if ($editSite && $editSite['logo']): ?>
                                <div style="margin-top:10px">
                                    <img src="../uploads/<?= htmlspecialchars($editSite['logo']) ?>" alt="Current Logo" style="max-width:100px;max-height:100px;object-fit:contain">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category">
                                <option value="vip" <?= ($editSite['category'] ?? '') === 'vip' ? 'selected' : '' ?>>VIP Sponsorlar</option>
                                <option value="guvenilir" <?= ($editSite['category'] ?? '') === 'guvenilir' ? 'selected' : '' ?>>Güvenilir Sponsorlar</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="show_in_marquee" value="1" <?= ($editSite['show_in_marquee'] ?? 0) ? 'checked' : '' ?>>
                            Marquee'de Göster
                        </label>
                    </div>

                    <div style="display:flex;gap:10px">
                        <button type="submit" class="btn btn-primary">
                            <?= $editSite ? '💾 Güncelle' : '➕ Ekle' ?>
                        </button>
                        <?php if ($editSite): ?>
                            <a href="sites.php" class="btn btn-warning">❌ İptal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sites List -->
        <h2 style="margin-top:40px">Mevcut Siteler (<?= count($sites) ?>)</h2>

        <?php foreach ($sites as $site): ?>
            <div class="site-card">
                <img src="../uploads/<?= htmlspecialchars($site['logo']) ?>" alt="<?= htmlspecialchars($site['name']) ?>" class="site-logo">
                <div class="site-info">
                    <h3><?= htmlspecialchars($site['name']) ?></h3>
                    <p>
                        <strong>URL:</strong> <?= htmlspecialchars($site['url']) ?><br>
                        <strong>Bonus:</strong> <?= htmlspecialchars($site['bonus_title']) ?><br>
                        <strong>Kategori:</strong> <span class="badge badge-<?= $site['category'] ?>"><?= strtoupper($site['category']) ?></span>
                        <?php if ($site['show_in_marquee']): ?>
                            <span class="badge" style="background:#17a2b8;color:white">MARQUEE</span>
                        <?php endif; ?>
                        <br>
                        <strong>Beğeni:</strong> ❤️ <?= $site['likes'] ?> |
                        <strong>Sıra:</strong> #<?= $site['display_order'] ?>
                    </p>
                </div>
                <div class="site-actions">
                    <a href="?edit=<?= $site['id'] ?>" class="btn btn-info">✏️ Düzenle</a>
                    <form method="POST" onsubmit="return confirm('Bu siteyi silmek istediğinizden emin misiniz?')" style="margin:0">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $site['id'] ?>">
                        <button type="submit" class="btn btn-danger">🗑️ Sil</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
