<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$search = isset($_GET['search']) ? $db->real_escape_string($_GET['search']) : '';
$filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 'services';

$results = [];
$title = '';

// ========== 1. ПОИСК ПО УСЛУГАМ ==========
if ($filter_type == 'services') {
    $title = 'Результаты поиска услуг';
    $results = $db->query("
        SELECT * FROM services 
        WHERE name LIKE '%$search%' 
        ORDER BY name
    ");
}

// ========== 2. ПОЛНОЦЕННЫЙ ПОИСК ПО ЗАПИСЯМ ==========
elseif ($filter_type == 'bookings') {
    $title = 'Результаты поиска по моим записям';
    
    $sql = "
        SELECT s.*, sv.name as service_name, tr.full_name as trainer_name
        FROM schedule s
        JOIN services sv ON s.service_id = sv.id
        LEFT JOIN users tr ON s.trainer_id = tr.id
        WHERE s.client_id = $user_id
    ";
    
    if (!empty($search)) {
        $sql .= " AND (
            sv.name LIKE '%$search%' 
            OR tr.full_name LIKE '%$search%' 
            OR s.status LIKE '%$search%'
            OR DATE(s.datetime) LIKE '%$search%'
            OR TIME(s.datetime) LIKE '%$search%'
        )";
    }
    
    $sql .= " ORDER BY s.datetime DESC LIMIT 50";
    
    $results = $db->query($sql);
    
    if (!$results) {
        echo "Ошибка SQL: " . $db->error;
        $results = [];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Поиск - Фитнес-портал</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>🔍 Поиск</h1>
        <div class="header-user">👤 <?= $_SESSION['user_name'] ?></div>
    </div>
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content">
            <h2>Поиск информации</h2>
            
            <div class="card">
                <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <input type="text" name="search" placeholder="Введите текст для поиска..." value="<?= htmlspecialchars($search) ?>" style="flex: 1; min-width: 200px;">
                    <select name="filter_type">
                        <option value="services" <?= $filter_type == 'services' ? 'selected' : '' ?>>Услуги</option>
                        <option value="bookings" <?= $filter_type == 'bookings' ? 'selected' : '' ?>>Мои записи</option>
                    </select>
                    <button type="submit" class="btn">🔍 Искать</button>
                </form>
            </div>
            
            <?php if ($search != ''): ?>
                <h3><?= $title ?> по запросу «<?= htmlspecialchars($search) ?>»</h3>
                
                <?php if ($results && $results->num_rows == 0): ?>
                    <div class="card">
                        <p>❌ Ничего не найдено. Попробуйте изменить запрос.</p>
                    </div>
                <?php elseif ($results): ?>
                    <div class="card">
                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <?php if ($filter_type == 'services'): ?>
                                        <th>Название</th>
                                        <th>Цена</th>
                                        <th>Длительность</th>
                                        <th></th>
                                    <?php else: ?>
                                        <th>Услуга</th>
                                        <th>Тренер</th>
                                        <th>Дата</th>
                                        <th>Время</th>
                                        <th>Статус</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $results->fetch_assoc()): ?>
                                    <tr>
                                        <?php if ($filter_type == 'services'): ?>
                                            <td><?= htmlspecialchars($row['name']) ?></td>
                                            <td><?= number_format($row['price'], 0, '.', ' ') ?> ₽</td>
                                            <td><?= $row['duration_minutes'] ?> мин</td>
                                            <td><a href="booking.php?service_id=<?= $row['id'] ?>" class="btn btn-sm">Записаться</a></td>
                                        <?php else: ?>
                                            <td><?= htmlspecialchars($row['service_name']) ?></td>
                                            <td><?= htmlspecialchars($row['trainer_name'] ?? '—') ?></td>
                                            <td><?= date('d.m.Y', strtotime($row['datetime'])) ?></td>
                                            <td><?= date('H:i', strtotime($row['datetime'])) ?></td>
                                            <td>
                                                <?php 
                                                if ($row['status'] == 'active') echo '✅ Активна';
                                                elseif ($row['status'] == 'cancelled') echo '❌ Отменена';
                                                else echo '📅 Завершена';
                                                ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>