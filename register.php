<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $db->real_escape_string($_POST['login']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $db->real_escape_string($_POST['full_name']);
    $phone = $db->real_escape_string($_POST['phone']);
    
    $check = $db->query("SELECT id FROM users WHERE login='$login'");
    if ($check->num_rows > 0) {
        $error = "Логин уже занят!";
    } else {
        $db->query("INSERT INTO users (login, password, full_name, phone, role) VALUES ('$login', '$password', '$full_name', '$phone', 'client')");
        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Регистрация - Фитнес-портал</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 500px;
            max-width: 100%;
            overflow: hidden;
        }
        
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .register-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .register-header p {
            opacity: 0.9;
        }
        
        .register-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        
        button {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        
        button:hover {
            opacity: 0.9;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>🏋️ Фитнес-портал</h1>
            <p>Создайте новый аккаунт</p>
        </div>
        <div class="register-body">
            <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Логин</label>
                    <input type="text" name="login" placeholder="Придумайте логин" required>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" placeholder="Придумайте пароль" required>
                </div>
                <div class="form-group">
                    <label>ФИО</label>
                    <input type="text" name="full_name" placeholder="Ваше полное имя" required>
                </div>
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="text" name="phone" placeholder="+7 (XXX) XXX-XX-XX">
                </div>
                <button type="submit">Зарегистрироваться</button>
            </form>
            <div class="login-link">
                Уже есть аккаунт? <a href="login.php">Войти</a>
            </div>
        </div>
    </div>
</body>
</html>