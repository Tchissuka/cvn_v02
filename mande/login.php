<?php
session_start();

// Redirecionar se já estiver logado
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validação com as novas credenciais
    if ($email === 'dr.jose@videiranguape.co.ao' && $password === 'naturopatia2026') {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_name'] = 'Dr. José Nguepe';
        $_SESSION['user_role'] = 'Naturopata Especialista';
        $_SESSION['user_email'] = $email;
        
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'E-mail ou senha inválidos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Videira Nguepe - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #27ae60;
            --accent-hover: #2ecc71;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* Elementos decorativos de fundo */
        body::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(39, 174, 96, 0.1) 0%, transparent 50%);
            animation: rotate 30s linear infinite;
        }

        body::after {
            content: '🌿';
            position: absolute;
            font-size: 300px;
            opacity: 0.05;
            bottom: -50px;
            right: -50px;
            transform: rotate(15deg);
            color: white;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            padding: 20px;
            position: relative;
            z-index: 10;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 45px;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--accent-color), var(--primary-color), var(--accent-color));
        }

        .login-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-circle {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            border: 4px solid var(--accent-color);
            box-shadow: 0 10px 20px rgba(39, 174, 96, 0.3);
            position: relative;
        }

        .logo-circle i {
            font-size: 45px;
            color: white;
        }

        .logo-circle::after {
            content: '';
            position: absolute;
            width: 120%;
            height: 120%;
            border: 2px dashed var(--accent-color);
            border-radius: 50%;
            animation: spin 20s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .login-header h2 {
            color: var(--primary-color);
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            color: var(--secondary-color);
            font-size: 15px;
            opacity: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .login-header p i {
            color: var(--accent-color);
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .form-group label i {
            margin-right: 8px;
            color: var(--accent-color);
            width: 20px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-color);
            font-size: 16px;
            opacity: 0.7;
        }

        .form-group input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid #e1e1e1;
            border-radius: 15px;
            font-size: 15px;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-group input:focus {
            border-color: var(--accent-color);
            outline: none;
            box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1);
            background: white;
        }

        .form-group input::placeholder {
            color: #999;
            font-size: 14px;
        }

        .error-message {
            background: #fee;
            color: #e74c3c;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-left: 4px solid #e74c3c;
            animation: shake 0.5s ease;
        }

        .error-message i {
            font-size: 18px;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--secondary-color);
            font-size: 14px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--accent-color);
        }

        .forgot-password {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .forgot-password:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(39, 174, 96, 0.3);
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn i {
            font-size: 18px;
        }

        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: var(--secondary-color);
            font-size: 14px;
        }

        .login-footer a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .login-footer a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        .demo-info {
            margin-top: 20px;
            padding: 15px;
            background: rgba(39, 174, 96, 0.1);
            border-radius: 12px;
            font-size: 13px;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 1px dashed var(--accent-color);
        }

        .demo-info i {
            color: var(--accent-color);
            font-size: 16px;
        }

        .demo-info strong {
            color: var(--primary-color);
        }

        /* Loader animation */
        .loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        .login-btn.loading .btn-text {
            display: none;
        }

        .login-btn.loading .loader {
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .login-header h2 {
                font-size: 24px;
            }
            
            .logo-circle {
                width: 80px;
                height: 80px;
                font-size: 32px;
            }
            
            .form-options {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-circle">
                    <i class="fas fa-leaf"></i>
                </div>
                <h2>Clínica Videira Nguepe</h2>
                <p>
                    <i class="fas fa-spa"></i>
                    Saúde Natural & Bem-estar
                </p>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label>
                        <i class="fas fa-envelope"></i>
                        E-mail
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="dr.jose@videiranguape.co.ao" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>
                        <i class="fas fa-lock"></i>
                        Senha
                    </label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <i class="fas fa-eye" id="togglePassword" style="left: auto; right: 15px; cursor: pointer;" onclick="togglePasswordVisibility()"></i>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> 
                        <i class="fas fa-check-circle"></i>
                        Lembrar-me
                    </label>
                    <a href="#" class="forgot-password">
                        <i class="fas fa-question-circle"></i>
                        Esqueceu a senha?
                    </a>
                </div>
                
                <button type="submit" class="login-btn" id="loginBtn">
                    <span class="btn-text">Entrar no Sistema</span>
                    <span class="loader"></span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
            
            <div class="login-footer">
                <p>
                    <i class="fas fa-phone-alt"></i>
                    Precisa de ajuda? 
                    <a href="#">
                        <i class="fas fa-headset"></i>
                        Contatar suporte
                    </a>
                </p>
                
              <div class="demo-info">
                <i class="fas fa-flask"></i>
                <div class="demo-details">
                    <span class="demo-label">Acesso demonstração:</span>
                    <div class="demo-credentials">
                        <span class="demo-email"><i class="far fa-envelope"></i> dr.jose@videiranguape.co.ao</span>
                        <span class="demo-separator">•</span>
                        <span class="demo-password"><i class="fas fa-key"></i> naturopatia2026</span>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Show loader on form submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (email && password) {
                document.getElementById('loginBtn').classList.add('loading');
            }
        });
        
        // Adicionar validação em tempo real
        document.getElementById('email').addEventListener('input', function() {
            this.style.borderColor = this.value.includes('@') ? 'var(--accent-color)' : '#e1e1e1';
        });
        
        // Animação de entrada nos campos
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        
        // Prevenir submissão com campos vazios
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos!');
            }
        });
        
        // Mensagem de boas-vindas
        console.log('🌿 Bem-vindo à Clínica Videira Nguepe - Cuidando da sua saúde naturalmente!');
    </script>
</body>
</html>