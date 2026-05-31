<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Начать смену
if (isset($_POST['start_shift'])) {
    $db->query("INSERT INTO shifts (trainer_id, start_time) VALUES ($user_id, NOW())");
    header('Location: shifts.php?started=1');
    exit;
}

// Закончить смену
if (isset($_POST['end_shift'])) {
    $db->query("UPDATE shifts SET end_time = NOW() WHERE trainer_id = $user_id AND end_time IS NULL");
    header('Location: shifts.php?ended=1');
    exit;
}

// Получить активную смену
$active_shift = $db->query("SELECT * FROM shifts WHERE trainer_id = $user_id AND end_time IS NULL")->fetch_assoc();

// Получить историю смен
if ($user_role == 'admin') {
    $shifts = $db->query("
        SELECT s.*, u.full_name as trainer_name 
        FROM shifts s 
        JOIN users u ON s.trainer_id = u.id 
        ORDER BY s.start_time DESC 
        LIMIT 100
    ");
} else {
    $shifts = $db->query("
        SELECT * FROM shifts 
        WHERE trainer_id = $user_id 
        ORDER BY start_time DESC 
        LIMIT 50
    ");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Смены и зарплата - Фитнес-портал</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>💰 Смены и зарплата</h1>
        <div class="header-user">👤 <?= $_SESSION['user_name'] ?></div>
    </div>
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content">
            <h2>Учёт рабочего времени</h2>
            
            <?php if (isset($_GET['started'])): ?>
                <div class="alert alert-success">✅ Смена начата!</div>
            <?php endif; ?>
            <?php if (isset($_GET['ended'])): ?>
                <div class="alert alert-success">✅ Смена завершена!</div>
            <?php endif; ?>
            
            <!-- Управление сменой -->
            <div class="card">
                <div class="card-title">Текущая смена</div>
                <?php if ($active_shift): ?>
                    <p>⏰ Смена начата: <?= date('d.m.Y H:i', strtotime($active_shift['start_time'])) ?></p>
                    <form method="POST">
                        <button type="submit" name="end_shift" class="btn">⏹️ Завершить смену</button>
                    </form>
                <?php else: ?>
                    <p>Нет активной смены</p>
                    <form method="POST">
                        <button type="submit" name="start_shift" class="btn">▶️ Начать смену</button>
                    </form>
                <?php endif; ?>
            </div>
            
            <!-- Ставка за час -->
            <div class="card">
                <div class="card-title">Часовая ставка</div>
                <p>💰 500 ₽ / час</p>
            </div>
            
            <!-- История смен -->
            <div class="card">
                <div class="card-title">История смен</div>
                <?php if ($shifts->num_rows == 0): ?>
                    <p>Нет завершённых смен</p>
                <?php else: ?>
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Начало</th>
                                <th>Конец</th>
                                <th>Длительность (ч)</th>
                                <th>Зарплата (₽)</th>
                                <?php if ($user_role == 'admin'): ?>
                                    <th>Тренер</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($shift = $shifts->fetch_assoc()): ?>
                                <?php 
                                if ($shift['end_time']) {
                                    $start = new DateTime($shift['start_time']);
                                    $end = new DateTime($shift['end_time']);
                                    $hours = $end->diff($start)->h + $end->diff($start)->i / 60;
                                    $salary = round($hours * 500);
                                } else {
                                    $hours = '—';
                                    $salary = '—';
                                }
                                ?>
                                <tr>
                                    <td><?= date('d.m.Y H:i', strtotime($shift['start_time'])) ?></td>
                                    <td><?= $shift['end_time'] ? date('d.m.Y H:i', strtotime($shift['end_time'])) : '—' ?></td>
                                    <td><?= is_numeric($hours) ? round($hours, 1) : $hours ?></td>
                                    <td><?= is_numeric($salary) ? number_format($salary, 0, '.', ' ') : $salary ?></td>
                                    <?php if ($user_role == 'admin'): ?>
                                        <td><?= htmlspecialchars($shift['trainer_name'] ?? '—') ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>