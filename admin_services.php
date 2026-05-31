<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Добавление
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = $db->real_escape_string($_POST['name']);
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];
    $db->query("INSERT INTO services (name, price, duration_minutes) VALUES ('$name', $price, $duration)");
    header('Location: admin_services.php?added=1');
    exit;
}

// Удаление
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->query("DELETE FROM services WHERE id = $id");
    header('Location: admin_services.php?deleted=1');
    exit;
}

// Изменение
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $name = $db->real_escape_string($_POST['name']);
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];
    $db->query("UPDATE services SET name='$name', price=$price, duration_minutes=$duration WHERE id=$id");
    header('Location: admin_services.php?edited=1');
    exit;
}

$services = $db->query("SELECT * FROM services ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Управление услугами</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="header">
        <h1>⚙️ Управление услугами</h1>
        <div class="header-user">👤 <?= $_SESSION['user_name'] ?></div>
    </div>
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content">
            <h2>Услуги фитнес-клуба</h2>
            
            <?php if (isset($_GET['added'])): ?>
                <div class="alert alert-success">Услуга добавлена</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Услуга удалена</div>
            <?php endif; ?>
            <?php if (isset($_GET['edited'])): ?>
                <div class="alert alert-success">Услуга обновлена</div>
            <?php endif; ?>
            
            <!-- Форма добавления -->
            <div class="card">
                <div class="card-title">Добавить услугу</div>
                <form method="POST">
                    <div class="form-group">
                        <label>Название</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Цена (₽)</label>
                        <input type="number" step="100" name="price" required>
                    </div>
                    <div class="form-group">
                        <label>Длительность (мин)</label>
                        <input type="number" name="duration" value="60" required>
                    </div>
                    <button type="submit" name="add">➕ Добавить</button>
                </form>
            </div>
            
            <!-- Список услуг -->
            <div class="card">
                <div class="card-title">Список услуг</div>
                <table style="width: 100%;">
                    <thead>
                        <tr><th>ID</th><th>Название</th><th>Цена</th><th>Длительность</th><th>Действия</th></tr>
                    </thead>
                    <tbody>
                        <?php while($service = $services->fetch_assoc()): ?>
                            <tr>
                                <td><?= $service['id'] ?></td>
                                <td><?= htmlspecialchars($service['name']) ?></td>
                                <td><?= number_format($service['price'], 0, '.', ' ') ?> ₽</td>
                                <td><?= $service['duration_minutes'] ?> мин</td>
                                <td>
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                        <input type="text" name="name" value="<?= htmlspecialchars($service['name']) ?>" style="width: 120px;">
                                        <input type="number" name="price" value="<?= $service['price'] ?>" style="width: 80px;">
                                        <input type="number" name="duration" value="<?= $service['duration_minutes'] ?>" style="width: 60px;">
                                        <button type="submit" name="edit" class="btn btn-sm">✏️</button>
                                    </form>
                                    <a href="?delete=<?= $service['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить?')">🗑️</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>