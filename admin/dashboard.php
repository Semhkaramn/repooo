<?php
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// İstatistikler
$totalSites = $pdo->query("SELECT COUNT(*) FROM sites")->fetchColumn();
$vipSites = $pdo->query("SELECT COUNT(*) FROM sites WHERE category = 'vip'")->fetchColumn();
$marqueeSites = $pdo->query("SELECT COUNT(*) FROM sites WHERE show_in_marquee = 1")->fetchColumn();
$totalLikes = $pdo->query("SELECT SUM(likes) FROM sites")->fetchColumn() ?? 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Slot Eser</title>
    <link rel="stylesheet" href="admin-style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>📊 Dashboard</h1>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🎰</div>
                <div class="stat-content">
                    <h3><?= $totalSites ?></h3>
                    <p>Toplam Site</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">⭐</div>
                <div class="stat-content">
                    <h3><?= $vipSites ?></h3>
                    <p>VIP Sponsorlar</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📢</div>
                <div class="stat-content">
                    <h3><?= $marqueeSites ?></h3>
                    <p>Marquee'de Gösterilen</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">❤️</div>
                <div class="stat-content">
                    <h3><?= $totalLikes ?></h3>
                    <p>Toplam Beğeni</p>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Hızlı İşlemler</h2>
            <div class="action-buttons">
                <a href="sites.php" class="btn btn-primary">
                    <span>➕</span> Yeni Site Ekle
                </a>
                <a href="banners.php" class="btn btn-success">
                    <span>🖼️</span> Banner Yönetimi
                </a>
                <a href="sites.php" class="btn btn-info">
                    <span>📝</span> Siteleri Yönet
                </a>
                <a href="../index.php" target="_blank" class="btn btn-warning">
                    <span>👁️</span> Siteyi Görüntüle
                </a>
            </div>
        </div>

        <div class="recent-sites">
            <h2>Son Eklenen Siteler</h2>
            <table>
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Site Adı</th>
                        <th>Kategori</th>
                        <th>Beğeni</th>
                        <th>Eklenme Tarihi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM sites ORDER BY created_at DESC LIMIT 5");
                    while ($site = $stmt->fetch()):
                    ?>
                    <tr>
                        <td><img src="../uploads/<?= htmlspecialchars($site['logo']) ?>" alt="Logo" style="width:40px;height:40px;object-fit:contain;"></td>
                        <td><?= htmlspecialchars($site['name']) ?></td>
                        <td><span class="badge badge-<?= $site['category'] ?>"><?= strtoupper($site['category']) ?></span></td>
                        <td><?= $site['likes'] ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($site['created_at'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
