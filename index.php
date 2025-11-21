<?php
require_once 'config.php';

// Get VIP sites
$vipSites = $pdo->query("SELECT * FROM sites WHERE category = 'vip' ORDER BY display_order ASC, created_at DESC")->fetchAll();

// Get Güvenilir sites
$guvenilirSites = $pdo->query("SELECT * FROM sites WHERE category = 'guvenilir' ORDER BY display_order ASC, created_at DESC")->fetchAll();

// Get marquee sites
$marqueeSites = $pdo->query("SELECT * FROM sites WHERE show_in_marquee = 1 ORDER BY display_order ASC")->fetchAll();

// Get left banner
$leftBanner = $pdo->query("SELECT b.*, s.url FROM fix_banners b LEFT JOIN sites s ON b.site_id = s.id WHERE b.position = 'left' AND b.is_active = 1 LIMIT 1")->fetch();

// Get right banner
$rightBanner = $pdo->query("SELECT b.*, s.url FROM fix_banners b LEFT JOIN sites s ON b.site_id = s.id WHERE b.position = 'right' AND b.is_active = 1 LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1, viewport-fit=cover">
    <meta name="description" content="Slot Eser, en iyi bahis bonuslarını ve casino fırsatlarını sunan güvenilir bir platformdur.">
    <meta name="keywords" content="Slot Eser, bahis bonusları, casino bonusları, yüksek oranlı bahis">
    <title>Slot Eser - En İyi Bahis ve Casino Bonusları</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Marquee Animation */
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .marquee-container {
            overflow: hidden;
            position: relative;
            background: #1a1a1a;
            padding: 20px 0;
        }

        .marquee-content {
            display: flex;
            animation: scroll 30s linear infinite;
        }

        .marquee-content:hover {
            animation-play-state: paused;
        }

        .marquee-item {
            flex-shrink: 0;
            padding: 0 20px;
        }

        .marquee-item img {
            height: 50px;
            object-fit: contain;
        }
    </style>
</head>
<body class="p-partners">
    <div id="app">
        <div class="layout">
            <!-- Main Content -->
            <div class="inc">
                <div class="container">
                    <!-- Fix Side Banners -->
                    <?php if ($leftBanner): ?>
                    <a href="<?= htmlspecialchars($leftBanner['url']) ?>" class="left fix-side" target="_blank">
                        <img src="uploads/<?= htmlspecialchars($leftBanner['banner_image']) ?>" width="100%" alt="Left Banner">
                    </a>
                    <?php endif; ?>

                    <?php if ($rightBanner): ?>
                    <a href="<?= htmlspecialchars($rightBanner['url']) ?>" class="right fix-side" target="_blank">
                        <img src="uploads/<?= htmlspecialchars($rightBanner['banner_image']) ?>" width="100%" alt="Right Banner">
                    </a>
                    <?php endif; ?>

                    <!-- Marquee Section -->
                    <?php if (!empty($marqueeSites)): ?>
                    <div class="marquee-container">
                        <div class="marquee-content">
                            <?php for($i=0; $i<2; $i++): // Duplicate for seamless loop ?>
                                <?php foreach($marqueeSites as $site): ?>
                                    <div class="marquee-item">
                                        <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank">
                                            <img src="uploads/<?= htmlspecialchars($site['logo']) ?>" alt="<?= htmlspecialchars($site['name']) ?>">
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- VIP SPONSORLAR -->
                    <h3 class="mb-1" style="margin-top:30px;font-size:24px;color:#333">VIP SPONSORLAR</h3>
                    <div class="row flex-wrap vip" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-bottom:40px">
                        <?php foreach($vipSites as $site): ?>
                        <div class="col">
                            <a class="partner card w-full" href="<?= htmlspecialchars($site['url']) ?>" target="_blank"
                               style="background-size:cover;--site-color:<?= htmlspecialchars($site['color']) ?>;
                                      display:block;background:#fff;border-radius:10px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.1);
                                      text-decoration:none;transition:transform 0.3s">
                                <div class="top" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px">
                                    <div class="heart" style="color:#999;font-size:14px">
                                        ❤️ <?= $site['likes'] ?>
                                    </div>
                                    <img src="uploads/<?= htmlspecialchars($site['logo']) ?>"
                                         alt="<?= htmlspecialchars($site['name']) ?>"
                                         style="height:50px;object-fit:contain">
                                </div>
                                <div class="bottom" style="color:#333">
                                    <h1 style="font-size:16px;font-weight:600;margin-bottom:5px">
                                        <?= htmlspecialchars($site['bonus_title']) ?>
                                    </h1>
                                    <span style="font-size:13px;color:#666">
                                        <?= htmlspecialchars($site['bonus_description']) ?>
                                    </span>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- GÜVENİLİR SPONSORLAR -->
                    <?php if (!empty($guvenilirSites)): ?>
                    <h3 class="mb-1" style="font-size:24px;color:#333">GÜVENİLİR SPONSORLAR</h3>
                    <div class="row flex-wrap vip" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px">
                        <?php foreach($guvenilirSites as $site): ?>
                        <div class="col">
                            <a class="partner card w-full" href="<?= htmlspecialchars($site['url']) ?>" target="_blank"
                               style="background-size:cover;--site-color:<?= htmlspecialchars($site['color']) ?>;
                                      display:block;background:#fff;border-radius:10px;padding:20px;box-shadow:0 2px 10px rgba(0,0,0,0.1);
                                      text-decoration:none;transition:transform 0.3s">
                                <div class="top" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:15px">
                                    <div class="heart" style="color:#999;font-size:14px">
                                        ❤️ <?= $site['likes'] ?>
                                    </div>
                                    <img src="uploads/<?= htmlspecialchars($site['logo']) ?>"
                                         alt="<?= htmlspecialchars($site['name']) ?>"
                                         style="height:50px;object-fit:contain">
                                </div>
                                <div class="bottom" style="color:#333">
                                    <h1 style="font-size:16px;font-weight:600;margin-bottom:5px">
                                        <?= htmlspecialchars($site['bonus_title']) ?>
                                    </h1>
                                    <span style="font-size:13px;color:#666">
                                        <?= htmlspecialchars($site['bonus_description']) ?>
                                    </span>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <footer style="background:#222;color:#fff;padding:40px 20px;text-align:center;margin-top:60px">
        <div style="max-width:1200px;margin:0 auto">
            <p>© 2024 Slot Eser - Tüm Hakları Saklıdır</p>
        </div>
    </footer>
</body>
</html>
