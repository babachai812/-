<?php
// menu.php - единое боковое меню для всех страниц
?>
<div class="sidebar">
    <a href="index.php" <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : '' ?>>🏠 Главная</a>
    <a href="dashboard.php" <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : '' ?>>📊 Мой кабинет</a>
    <a href="trainers.php" <?= basename($_SERVER['PHP_SELF']) == 'trainers.php' ? 'class="active"' : '' ?>>👨‍🏫 Наши тренеры</a>
    
    <!-- ПОИСК - ДЛЯ ВСЕХ АВТОРИЗОВАННЫХ ПОЛЬЗОВАТЕЛЕЙ -->
    <a href="search.php" <?= basename($_SERVER['PHP_SELF']) == 'search.php' ? 'class="active"' : '' ?>>🔍 Поиск</a>
    
    <?php if (isset($_SESSION['user_role'])): ?>
        <?php if ($_SESSION['user_role'] == 'client'): ?>
            <a href="services.php" <?= basename($_SERVER['PHP_SELF']) == 'services.php' ? 'class="active"' : '' ?>>💪 Услуги</a>
            <a href="my_bookings.php" <?= basename($_SERVER['PHP_SELF']) == 'my_bookings.php' ? 'class="active"' : '' ?>>📅 Мои записи</a>
        <?php endif; ?>
        
        <?php if ($_SESSION['user_role'] == 'admin'): ?>
            <a href="admin_users.php" <?= basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'class="active"' : '' ?>>👥 Пользователи</a>
            <a href="admin_services.php" <?= basename($_SERVER['PHP_SELF']) == 'admin_services.php' ? 'class="active"' : '' ?>>⚙️ Услуги (CRUD)</a>
            <a href="schedule_admin.php" <?= basename($_SERVER['PHP_SELF']) == 'schedule_admin.php' ? 'class="active"' : '' ?>>📅 Расписание</a>
            <a href="reports.php" <?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'class="active"' : '' ?>>📈 Отчёты</a>
            <a href="shifts.php" <?= basename($_SERVER['PHP_SELF']) == 'shifts.php' ? 'class="active"' : '' ?>>💰 Смены и зарплата</a>
        <?php endif; ?>
        
        <?php if ($_SESSION['user_role'] == 'trainer'): ?>
            <a href="trainer_schedule.php" <?= basename($_SERVER['PHP_SELF']) == 'trainer_schedule.php' ? 'class="active"' : '' ?>>📅 Моё расписание</a>
            <a href="shifts.php" <?= basename($_SERVER['PHP_SELF']) == 'shifts.php' ? 'class="active"' : '' ?>>💰 Мои смены</a>
        <?php endif; ?>
        
        <a href="feedback.php" <?= basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'class="active"' : '' ?>>✍️ Отзывы</a>
        <a href="history.php" <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'class="active"' : '' ?>>📜 История</a>
        <a href="logout.php">🚪 Выход</a>
        
    <?php else: ?>
        <!-- Если пользователь не авторизован -->
        <a href="login.php" <?= basename($_SERVER['PHP_SELF']) == 'login.php' ? 'class="active"' : '' ?>>🔑 Вход</a>
        <a href="register.php" <?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'class="active"' : '' ?>>📝 Регистрация</a>
        <a href="feedback.php" <?= basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'class="active"' : '' ?>>✍️ Отзывы</a>
    <?php endif; ?>
</div>