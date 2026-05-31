<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Добавление отзыва
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_feedback'])) {
    $rating = (int)$_POST['rating'];
    $comment = $db->real_escape_string($_POST['comment']);
    $db->query("INSERT INTO feedback (client_id, rating, comment) VALUES ($user_id, $rating, '$comment')");
    header('Location: feedback.php?added=1');
    exit;
}

// Удаление отзыва (только для админа)
if (isset($_GET['delete']) && $user_role == 'admin') {
    $feedback_id = (int)$_GET['delete'];
    $db->query("DELETE FROM feedback WHERE id = $feedback_id");
    header('Location: feedback.php?deleted=1');
    exit;
}

// Получаем список отзывов
$feedbacks = $db->query("
    SELECT f.*, u.login, u.full_name 
    FROM feedback f 
    JOIN users u ON f.client_id = u.id 
    ORDER BY f.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Отзывы - Фитнес-портал</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .rating {
            color: #ffc107;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .feedback-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .feedback-author {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .feedback-date {
            color: #999;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .feedback-comment {
            margin: 10px 0;
            line-height: 1.5;
        }
        .star-active { color: #ffc107; }
        .star-inactive { color: #e4e5e9; }
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
            <h2>Отзывы наших клиентов</h2>
            
            <?php if (isset($_GET['added'])): ?>
                <div class="alert alert-success">Спасибо за ваш отзыв!</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Отзыв удалён</div>
            <?php endif; ?>
            
            <!-- Форма добавления отзыва -->
            <div class="card">
                <div class="card-title">Оставить отзыв</div>
                <form method="POST">
                    <div class="form-group">
                        <label>Оценка</label>
                        <select name="rating" required>
                            <option value="5">⭐️⭐️⭐️⭐️⭐️ Отлично</option>
                            <option value="4">⭐️⭐️⭐️⭐️ Хорошо</option>
                            <option value="3">⭐️⭐️⭐️ Нормально</option>
                            <option value="2">⭐️⭐️ Плохо</option>
                            <option value="1">⭐️ Ужасно</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Комментарий</label>
                        <textarea name="comment" rows="4" placeholder="Поделитесь впечатлениями..." required style="width: 100%; border-radius: 10px; border: 1px solid #ddd; padding: 10px;"></textarea>
                    </div>
                    <button type="submit" name="add_feedback">📝 Отправить отзыв</button>
                </form>
            </div>
            
            <!-- Список отзывов -->
            <h3>Все отзывы</h3>
            <?php if ($feedbacks->num_rows == 0): ?>
                <div class="card">
                    <p>Пока нет отзывов. Будьте первым!</p>
                </div>
            <?php else: ?>
                <?php while($fb = $feedbacks->fetch_assoc()): ?>
                    <div class="feedback-card">
                        <div class="rating">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $fb['rating']): ?>
                                    <span class="star-active">★</span>
                                <?php else: ?>
                                    <span class="star-inactive">☆</span>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="feedback-author"><?= htmlspecialchars($fb['full_name'] ?? $fb['login']) ?></div>
                        <div class="feedback-date"><?= date('d.m.Y H:i', strtotime($fb['created_at'])) ?></div>
                        <div class="feedback-comment"><?= nl2br(htmlspecialchars($fb['comment'])) ?></div>
                        <?php if ($user_role == 'admin'): ?>
                            <a href="?delete=<?= $fb['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить отзыв?')">🗑️ Удалить</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>