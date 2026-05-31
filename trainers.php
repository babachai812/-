<?php
require 'config.php';

$trainers = $db->query("SELECT * FROM users WHERE role = 'trainer' ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Наши тренеры - FitnessClub</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .trainers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .trainer-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }
        .trainer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .trainer-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
        }
        .trainer-info {
            padding: 20px;
        }
        .trainer-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .trainer-specialty {
            color: #667eea;
            margin-bottom: 15px;
        }
        .trainer-desc {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
        .btn-outline {
            background: transparent;
            border: 2px solid #667eea;
            color: #667eea;
            margin-top: 15px;
        }
        .btn-outline:hover {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
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
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content" style="padding: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 36px; margin-bottom: 10px;">Наши профессиональные тренеры</h1>
                <p style="color: #666;">Опытные специалисты, которые помогут достичь ваших целей</p>
            </div>
            
            <div class="trainers-grid">
                <?php 
                $avatars = ['🏋️', '🧘', '💪', '🤸', '🏃', '🚴'];
                $specialties = ['Персональный тренер', 'Фитнес-инструктор', 'Йога-коуч', 'Кроссфит-тренер', 'Бокс/ММА', 'Пилатес-инструктор'];
                $i = 0;
                while($trainer = $trainers->fetch_assoc()): 
                ?>
                <div class="trainer-card">
                    <div class="trainer-avatar"><?= $avatars[$i % count($avatars)] ?></div>
                    <div class="trainer-info">
                        <div class="trainer-name"><?= htmlspecialchars($trainer['full_name']) ?></div>
                        <div class="trainer-specialty"><?= $specialties[$i % count($specialties)] ?></div>
                        <div class="trainer-desc">Стаж работы: более 5 лет. 
                        <?php if ($i % 2 == 0): ?>
                        Сертифицированный специалист по функциональному тренингу.
                        <?php else: ?>
                        Эксперт в области снижения веса и коррекции фигуры.
                        <?php endif; ?>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="services.php" class="btn btn-sm btn-outline" style="display: inline-block;">Записаться</a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-sm btn-outline" style="display: inline-block;">Регистрация</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php $i++; endwhile; ?>
            </div>
        </div>
    </div>
    
    <div style="background: #1a1a2e; color: white; padding: 40px 20px; text-align: center; margin-top: 40px;">
        <p>🏋️ FitnessClub — Профессиональный фитнес-клуб</p>
        <p style="margin-top: 10px; opacity: 0.7;">📍 г. Москва, ул. Спортивная, 15 | 📞 +7 (495) 123-45-67</p>
    </div>
</body>
</html>