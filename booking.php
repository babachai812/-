<?php
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$client_id = $_SESSION['user_id'];
$service_id = (int)$_GET['service_id'];
$service = $db->query("SELECT * FROM services WHERE id=$service_id")->fetch_assoc();
$trainers = $db->query("SELECT * FROM users WHERE role='trainer'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trainer_id = (int)$_POST['trainer_id'];
    $datetime = $db->real_escape_string($_POST['datetime']);
    $db->query("INSERT INTO schedule (service_id, client_id, trainer_id, datetime, status) VALUES ($service_id, $client_id, $trainer_id, '$datetime', 'active')");
    
    // Логируем действие
    $booking_id = $db->insert_id;
    $db->query("INSERT INTO history_log (user_id, action_type, table_name, record_id) VALUES ($client_id, 'create_booking', 'schedule', $booking_id)");
    
    header('Location: dashboard.php?booked=1');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Запись на тренировку</title>
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
            <div class="card">
                <div class="card-title">Запись на: <?= htmlspecialchars($service['name']) ?></div>
                <p>💰 Цена: <?= number_format($service['price'], 0, '.', ' ') ?> ₽</p>
                <form method="POST">
                    <div class="form-group">
                        <label>Выберите тренера</label>
                        <select name="trainer_id" required>
                            <option value="">-- Выберите тренера --</option>
                            <?php while($trainer = $trainers->fetch_assoc()): ?>
                                <option value="<?= $trainer['id'] ?>"><?= htmlspecialchars($trainer['full_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Дата и время</label>
                        <input type="datetime-local" name="datetime" required>
                    </div>
                    <button type="submit">✅ Подтвердить запись</button>
                    <a href="services.php" class="btn" style="background: #ccc;">← Назад</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>