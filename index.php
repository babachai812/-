<?php
require 'config.php';

// Получаем последние 3 отзыва для главной
$latest_feedback = $db->query("
    SELECT f.*, u.full_name, u.login 
    FROM feedback f 
    JOIN users u ON f.client_id = u.id 
    ORDER BY f.created_at DESC 
    LIMIT 3
");

// Получаем услуги для главной
$services_main = $db->query("SELECT * FROM services LIMIT 6");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitnessClub | Профессиональный фитнес-клуб в Москве</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Герой-секция */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            animation: fadeInDown 0.8s ease;
        }
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.9;
            animation: fadeInUp 0.8s ease;
        }
        .hero .btn-large {
            padding: 15px 40px;
            font-size: 18px;
            background: white;
            color: #667eea;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .hero .btn-large:hover {
            transform: scale(1.05);
            background: #f0f0f0;
        }
        
        /* Секция преимуществ */
        .features {
            padding: 60px 0;
            background: #f8f9ff;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .feature-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        .feature-card h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .feature-card p {
            color: #666;
            line-height: 1.6;
        }
        
        /* Секция услуг */
        .services-section {
            padding: 60px 0;
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
        }
        .section-title {
            text-align: center;
            font-size: 36px;
            margin-bottom: 40px;
            color: #333;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .service-item {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .service-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .service-img {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
        }
        .service-info {
            padding: 20px;
        }
        .service-info h3 {
            margin-bottom: 10px;
        }
        .service-price {
            color: #667eea;
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        /* Отзывы на главной */
        .reviews-section {
            background: #f8f9ff;
            padding: 60px 0;
        }
        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .review-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .review-rating {
            color: #ffc107;
            font-size: 18px;
            margin-bottom: 15px;
        }
        .review-text {
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .review-author {
            font-weight: bold;
            color: #667eea;
        }
        
        /* Призыв к действию */
        .cta {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 60px 20px;
        }
        .cta h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        .cta .btn-white {
            background: white;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        
        /* Анимации */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Шапка -->
    <div class="header">
        <h1>🏋️ FitnessClub</h1>
        <div class="header-user">
            <?php if (isset($_SESSION['user_id'])): ?>
                👤 <a href="dashboard.php" style="color: white;"><?= $_SESSION['user_name'] ?></a>
                | <a href="logout.php" style="color: white;">Выйти</a>
            <?php else: ?>
                <a href="login.php" style="color: white;">Вход</a>
                | <a href="register.php" style="color: white;">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Герой-секция -->
    <div class="hero">
        <h1>Добро пожаловать в FitnessClub</h1>
        <p>Лучший фитнес-клуб с профессиональными тренерами и современным оборудованием</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-large">Начать тренировки →</a>
        <?php else: ?>
            <a href="dashboard.php" class="btn btn-large">Личный кабинет →</a>
        <?php endif; ?>
    </div>
    
    <!-- Преимущества -->
    <div class="features">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🏆</div>
                <h3>Профессиональные тренеры</h3>
                <p>Сертифицированные инструкторы с многолетним опытом</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💪</div>
                <h3>Современное оборудование</h3>
                <p>Новейшие тренажёры от ведущих мировых брендов</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Удобное расписание</h3>
                <p>Занятия в любое время с 7:00 до 23:00</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">❤️</div>
                <h3>Индивидуальный подход</h3>
                <p>Программы тренировок под ваши цели</p>
            </div>
        </div>
    </div>
    
    <!-- Услуги -->
    <div class="services-section">
        <h2 class="section-title">Наши услуги</h2>
        <div class="services-grid">
            <?php while($service = $services_main->fetch_assoc()): ?>
            <div class="service-item">
                <div class="service-img">
                    <?php 
                    $icons = ['💪', '🧘', '🏃', '🤸', '🏋️', '🚴'];
                    echo $icons[array_rand($icons)];
                    ?>
                </div>
                <div class="service-info">
                    <h3><?= htmlspecialchars($service['name']) ?></h3>
                    <p>⏱️ <?= $service['duration_minutes'] ?> минут</p>
                    <div class="service-price"><?= number_format($service['price'], 0, '.', ' ') ?> ₽</div>
                    <a href="<?= isset($_SESSION['user_id']) ? 'booking.php?service_id=' . $service['id'] : 'login.php' ?>" class="btn" style="display: inline-block;">Записаться →</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <!-- Отзывы -->
    <?php if ($latest_feedback->num_rows > 0): ?>
    <div class="reviews-section">
        <h2 class="section-title">Что говорят наши клиенты</h2>
        <div class="reviews-grid">
            <?php while($review = $latest_feedback->fetch_assoc()): ?>
            <div class="review-card">
                <div class="review-rating">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <?= $i <= $review['rating'] ? '★' : '☆' ?>
                    <?php endfor; ?>
                </div>
                <div class="review-text">"<?= htmlspecialchars(mb_substr($review['comment'], 0, 100)) . (mb_strlen($review['comment']) > 100 ? '...' : '') ?>"</div>
                <div class="review-author">— <?= htmlspecialchars($review['full_name'] ?? $review['login']) ?></div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Призыв к действию -->
    <div class="cta">
        <h2>Готовы начать свой путь к здоровому телу?</h2>
        <p>Присоединяйтесь к FitnessClub уже сегодня!</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn-white">Зарегистрироваться бесплатно →</a>
        <?php else: ?>
            <a href="services.php" class="btn-white">Выбрать тренировку →</a>
        <?php endif; ?>
    </div>
    
    <!-- Подвал -->
    <div style="background: #1a1a2e; color: white; padding: 40px 20px; text-align: center;">
        <p>🏋️ FitnessClub — Профессиональный фитнес-клуб</p>
        <p style="margin-top: 10px; opacity: 0.7;">📍 г. Тюмень, ул. Газовиков, 65 | 📞 +7 (495) 123-45-67</p>
        <p style="margin-top: 20px; font-size: 12px; opacity: 0.5;">© 2024 FitnessClub. Все права защищены.</p>
    </div>
</body>
</html>