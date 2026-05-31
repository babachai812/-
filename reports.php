<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header('Location: login.php');
    exit;
}

$period = isset($_GET['period']) ? $_GET['period'] : 'month';
$year = date('Y');
$month = date('m');

// Формируем условия для SQL в зависимости от периода
if ($period == 'month') {
    $group_by = "DATE_FORMAT(s.datetime, '%Y-%m-%d')";
    $title = "Анализ за текущий месяц";
    $sql_where = "MONTH(s.datetime) = $month AND YEAR(s.datetime) = $year AND s.status = 'completed'";
} elseif ($period == 'quarter') {
    $group_by = "DATE_FORMAT(s.datetime, '%Y-%m')";
    $title = "Анализ за текущий квартал";
    $quarter_start = date('Y-m-01', strtotime('-' . ((date('n') - 1) % 3) . ' months'));
    $sql_where = "s.datetime >= '$quarter_start' AND s.status = 'completed'";
} else {
    $group_by = "DATE_FORMAT(s.datetime, '%Y-%m')";
    $title = "Анализ за текущий год";
    $sql_where = "YEAR(s.datetime) = $year AND s.status = 'completed'";
}

// Доход по услугам
$income_data = $db->query("
    SELECT sv.name, SUM(sv.price) as total
    FROM schedule s
    JOIN services sv ON s.service_id = sv.id
    WHERE $sql_where
    GROUP BY sv.id
    ORDER BY total DESC
    LIMIT 10
");

// Динамика дохода за период
$trend_data = $db->query("
    SELECT $group_by as period, SUM(sv.price) as total
    FROM schedule s
    JOIN services sv ON s.service_id = sv.id
    WHERE YEAR(s.datetime) = $year AND s.status = 'completed'
    GROUP BY period
    ORDER BY period
");

// Количество клиентов
$clients_count = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'client'")->fetch_assoc()['count'];

// Количество тренеров
$trainers_count = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'trainer'")->fetch_assoc()['count'];

// Всего записей
$total_bookings = $db->query("SELECT COUNT(*) as count FROM schedule")->fetch_assoc()['count'];

// Доход за период
$total_income = $db->query("
    SELECT SUM(sv.price) as total
    FROM schedule s
    JOIN services sv ON s.service_id = sv.id
    WHERE $sql_where
")->fetch_assoc()['total'] ?? 0;

// Подготовка данных для графиков
$income_labels = [];
$income_values = [];
while($row = $income_data->fetch_assoc()) {
    $income_labels[] = $row['name'];
    $income_values[] = $row['total'];
}

$trend_labels = [];
$trend_values = [];
while($row = $trend_data->fetch_assoc()) {
    $trend_labels[] = $row['period'];
    $trend_values[] = $row['total'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Аналитика и отчёты</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        .chart-container {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }
        .stat-number {
            font-size: 36px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Аналитика фитнес-клуба</h1>
        <div class="header-user">👤 <?= $_SESSION['user_name'] ?></div>
    </div>
    
    <div class="container">
        <?php include 'menu.php'; ?>
        
        <div class="content">
            <h2>Анализ работы клуба</h2>
            
            <!-- Фильтр периода -->
            <div class="card">
                <form method="GET" style="display: flex; gap: 10px;">
                    <select name="period">
                        <option value="month" <?= $period == 'month' ? 'selected' : '' ?>>За месяц</option>
                        <option value="quarter" <?= $period == 'quarter' ? 'selected' : '' ?>>За квартал</option>
                        <option value="year" <?= $period == 'year' ? 'selected' : '' ?>>За год</option>
                    </select>
                    <button type="submit" class="btn">Применить</button>
                </form>
            </div>
            
            <!-- Статистика -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($total_income, 0, '.', ' ') ?> ₽</div>
                    <div>Доход за период</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $total_bookings ?></div>
                    <div>Всего записей</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $clients_count ?></div>
                    <div>Клиентов</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $trainers_count ?></div>
                    <div>Тренеров</div>
                </div>
            </div>
            
            <!-- Графики -->
            <div class="charts-grid">
                <div class="chart-container">
                    <h3>Доход по услугам</h3>
                    <canvas id="incomeChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Динамика дохода</h3>
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // График доходов по услугам
        const ctx1 = document.getElementById('incomeChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= json_encode($income_labels) ?>,
                datasets: [{
                    label: 'Доход (₽)',
                    data: <?= json_encode($income_values) ?>,
                    backgroundColor: 'rgba(102, 126, 234, 0.7)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        
        // График динамики дохода
        const ctx2 = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: <?= json_encode($trend_labels) ?>,
                datasets: [{
                    label: 'Доход (₽)',
                    data: <?= json_encode($trend_values) ?>,
                    backgroundColor: 'rgba(118, 75, 162, 0.2)',
                    borderColor: 'rgba(118, 75, 162, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>