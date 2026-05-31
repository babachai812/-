<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Отмена записи
if (isset($_GET['cancel'])) {
    $booking_id = (int)$_GET['cancel'];
    $db->query("UPDATE schedule SET status = 'cancelled' WHERE id = $booking_id AND client_id = $user_id");
    
    // Логируем отмену
    $db->query("INSERT INTO history_log (user_id, action_type, table_name, record_id) VALUES ($user_id, 'cancel_booking', 'schedule', $booking_id)");
    
    header('Location: my_bookings.php?cancelled=1');
    exit;
}

// Получаем список записей пользователя
$bookings = $db->query("
    SELECT s.*, sv.name as service_name, sv.price, u.full_name as trainer_name 
    FROM schedule s 
    JOIN services sv ON s.service_id = sv.id 
    LEFT JOIN users u ON s.trainer_id = u.id 
    WHERE s.client_id = $user_id 
    ORDER BY s.datetime DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Мои записи - Фитнес-портал</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Дополнительные стили для карточек записей */
        .booking-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .booking-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .booking-service {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #667eea;
            display: inline-block;
        }
        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin: 15px 0;
        }
        .booking-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #555;
        }
        .booking-detail .emoji {
            font-size: 20px;
        }
        .booking-price {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .status-completed {
            background: #e2e3e5;
            color: #383d41;
        }
        .btn-cancel {
            background: #dc3545;
            margin-top: 10px;
        }
        .btn-cancel:hover {
            background: #c82333;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }
        .empty-state .emoji {
            font-size: 64px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏋️ Фитнес-портал</h1>
        <div class="header-user">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></div>
    </div>
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content">
            <h2>📅 Мои записи</h2>
            <p style="color: #666; margin-bottom: 25px;">Здесь отображаются все ваши записи на тренировки</p>
            
            <?php if (isset($_GET['cancelled'])): ?>
                <div class="alert alert-success">✅ Запись успешно отменена</div>
            <?php endif; ?>
            
            <?php if ($bookings->num_rows == 0): ?>
                <div class="empty-state">
                    <div class="emoji">📭</div>
                    <h3>У вас пока нет записей</h3>
                    <p style="margin: 15px 0; color: #666;">Запишитесь на первую тренировку и начните свой путь к здоровому телу!</p>
                    <a href="services.php" class="btn">💪 Записаться на тренировку</a>
                </div>
            <?php else: ?>
                <?php while($booking = $bookings->fetch_assoc()): ?>
                    <div class="booking-card">
                        <div class="booking-service">
                            <?= htmlspecialchars($booking['service_name']) ?>
                        </div>
                        
                        <div class="booking-details">
                            <div class="booking-detail">
                                <span class="emoji">📅</span>
                                <span><?= date('d.m.Y', strtotime($booking['datetime'])) ?></span>
                            </div>
                            <div class="booking-detail">
                                <span class="emoji">⏰</span>
                                <span><?= date('H:i', strtotime($booking['datetime'])) ?></span>
                            </div>
                            <div class="booking-detail">
                                <span class="emoji">🧑‍🏫</span>
                                <span>Тренер: <?= htmlspecialchars($booking['trainer_name'] ?? 'Не назначен') ?></span>
                            </div>
                            <div class="booking-detail">
                                <span class="emoji">💰</span>
                                <span class="booking-price"><?= number_format($booking['price'], 0, '.', ' ') ?> ₽</span>
                            </div>
                            <div class="booking-detail">
                                <span class="emoji">📌</span>
                                <span>Статус: 
                                    <?php 
                                    $status_class = '';
                                    $status_text = '';
                                    if ($booking['status'] == 'active') {
                                        $status_class = 'status-active';
                                        $status_text = 'Активна';
                                    } elseif ($booking['status'] == 'cancelled') {
                                        $status_class = 'status-cancelled';
                                        $status_text = 'Отменена';
                                    } else {
                                        $status_class = 'status-completed';
                                        $status_text = 'Завершена';
                                    }
                                    ?>
                                    <span class="status-badge <?= $status_class ?>"><?= $status_text ?></span>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($booking['status'] == 'active'): ?>
                            <a href="?cancel=<?= $booking['id'] ?>" class="btn btn-cancel" onclick="return confirm('❓ Вы уверены, что хотите отменить запись?')">❌ Отменить запись</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>