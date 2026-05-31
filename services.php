<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$services = $db->query("SELECT * FROM services ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Услуги - Фитнес-портал</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>🏋️ Фитнес-портал</h1>
        <div class="header-user">👤 <?= $_SESSION['user_name'] ?></div>
    </div>
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content">
            <h2>Наши услуги</h2>
            <div class="stats-grid">
                <?php while($service = $services->fetch_assoc()): ?>
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($service['price'], 0, '.', ' ') ?> ₽</div>
                    <div class="stat-label"><?= htmlspecialchars($service['name']) ?></div>
                    <div style="margin-top: 10px;">⏱️ <?= $service['duration_minutes'] ?> мин</div>
                    <a href="booking.php?service_id=<?= $service['id'] ?>" class="btn" style="margin-top: 15px; display: inline-block;">Записаться</a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>