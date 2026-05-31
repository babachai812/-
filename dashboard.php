<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// Получаем статистику
$bookings_count = $db->query("SELECT COUNT(*) as count FROM schedule WHERE client_id = $user_id AND status = 'active'")->fetch_assoc()['count'];

// Доход за месяц (для админа)
$month_income = 0;
if ($user_role == 'admin') {
    $income = $db->query("SELECT SUM(sv.price) as total FROM schedule s JOIN services sv ON s.service_id = sv.id WHERE MONTH(s.datetime) = MONTH(NOW()) AND YEAR(s.datetime) = YEAR(NOW()) AND s.status = 'completed'")->fetch_assoc();
    $month_income = $income['total'] ?? 0;
}

// Количество клиентов (для админа)
$clients_count = 0;
if ($user_role == 'admin') {
    $clients_count = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'client'")->fetch_assoc()['count'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Фитнес-портал - Личный кабинет</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>🏋️ Фитнес-портал</h1>
        <div class="header-user">
            👤 <?= htmlspecialchars($user_name) ?> (<?= $user_role ?>)
        </div>
    </div>
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content">
            <h2>Добро пожаловать, <?= htmlspecialchars($user_name) ?>!</h2>
            <p style="margin-bottom: 20px; color: #666;">Рады видеть вас в фитнес-портале</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $bookings_count ?></div>
                    <div class="stat-label">Активных записей</div>
                </div>
                <?php if ($user_role == 'admin'): ?>
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($month_income, 0, '.', ' ') ?> ₽</div>
                    <div class="stat-label">Доход за месяц</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $clients_count ?></div>
                    <div class="stat-label">Всего клиентов</div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <div class="card-title">Последние действия</div>
                <p>Здесь будет отображаться ваша активность в системе.</p>
            </div>
        </div>
    </div>
</body>
</html>