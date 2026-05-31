<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $db->real_escape_string($_POST['login']);
    $password = $_POST['password'];
    
    $result = $db->query("SELECT * FROM users WHERE login='$login'");
    
    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_login'] = $user['login'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Неверный пароль!";
        }
    } else {
        $error = "Пользователь не найден!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Вход - Фитнес-портал</title>
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
        }
        
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 450px;
            max-width: 90%;
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .login-header p {
            opacity: 0.9;
        }
        
        .login-body {
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
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .register-link a {
            color: #667eea;
            text-decoration: none;
        }
        
        .register-link a:hover {
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
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>🏋️ Фитнес-портал</h1>
            <p>Войдите в свой аккаунт</p>
        </div>
        <div class="login-body">
            <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
            <?php if (isset($_GET['registered'])) echo "<div class='success'>Регистрация успешна! Войдите.</div>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Логин</label>
                    <input type="text" name="login" placeholder="Введите логин" required>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" placeholder="Введите пароль" required>
                </div>
                <button type="submit">Войти</button>
            </form>
            <div class="register-link">
                Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
            </div>
        </div>
    </div>
</body>
</html>