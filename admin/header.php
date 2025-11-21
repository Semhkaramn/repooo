<header class="admin-header">
    <div class="header-left">
        <h2>🎰 Slot Eser Admin</h2>
    </div>
    <nav class="header-nav">
        <a href="dashboard.php" class="nav-link">Dashboard</a>
        <a href="sites.php" class="nav-link">Siteler</a>
        <a href="banners.php" class="nav-link">Bannerlar</a>
        <a href="settings.php" class="nav-link">Ayarlar</a>
    </nav>
    <div class="header-right">
        <span class="admin-name">👤 <?= htmlspecialchars($_SESSION['admin_username']) ?></span>
        <a href="logout.php" class="btn btn-logout">Çıkış</a>
    </div>
</header>
