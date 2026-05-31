<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];

// Админ видит все действия, обычный пользователь - только свои
if ($user_role == 'admin') {
    $history = $db->query("
        SELECT hl.*, u.login, u.full_name 
        FROM history_log hl 
        JOIN users u ON hl.user_id = u.id 
        ORDER BY hl.timestamp DESC 
        LIMIT 100
    ");
} else {
    $history = $db->query("
        SELECT * FROM history_log 
        WHERE user_id = $user_id 
        ORDER BY timestamp DESC 
        LIMIT 50
    ");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>История операций - Фитнес-портал</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .action-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .history-item {
            transition: background 0.2s;
        }
        .history-item:hover {
            background: #f8f9ff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏋️ Фитнес-портал</h1>
        <div class="header-user">👤 <?= $_SESSION['user_name'] ?></div>
    </div>
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content">
            <h2>История операций</h2>
            <p style="margin-bottom: 20px; color: #666;">Здесь отображаются все действия, совершённые в системе</p>
            
            <?php if ($history->num_rows == 0): ?>
                <div class="card">
                    <p>История операций пуста</p>
                </div>
            <?php else: ?>
                <div class="card">
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Время</th>
                                <th>Пользователь</th>
                                <th>Действие</th>
                                <th>Таблица</th>
                                <th>ID записи</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($log = $history->fetch_assoc()): ?>
                                <tr class="history-item">
                                    <td style="white-space: nowrap;"><?= date('d.m.Y H:i:s', strtotime($log['timestamp'])) ?></td>
                                    <td>
                                        <?= htmlspecialchars($log['full_name'] ?? $log['login'] ?? $log['user_id']) ?>
                                        <?php if ($user_role == 'admin'): ?>
                                            <span style="color: #999; font-size: 12px;">(ID: <?= $log['user_id'] ?>)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $action_text = '';
                                        switch($log['action_type']) {
                                            case 'create_booking': $action_text = '📝 Создание записи'; break;
                                            case 'cancel_booking': $action_text = '❌ Отмена записи'; break;
                                            case 'add_service': $action_text = '➕ Добавление услуги'; break;
                                            case 'edit_service': $action_text = '✏️ Редактирование услуги'; break;
                                            case 'delete_service': $action_text = '🗑️ Удаление услуги'; break;
                                            case 'register': $action_text = '📝 Регистрация'; break;
                                            case 'login': $action_text = '🔑 Вход в систему'; break;
                                            default: $action_text = '⚙️ ' . $log['action_type']; break;
                                        }
                                        echo $action_text;
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($log['table_name']) ?></td>
                                    <td><?= $log['record_id'] ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            
            <div class="card" style="margin-top: 20px;">
                <div class="card-title">💡 Примечание</div>
                <p>История операций автоматически сохраняет все важные действия: регистрацию, запись на тренировку, отмену, добавление/редактирование услуг и т.д.</p>
            </div>
        </div>
    </div>
</body>
</html>