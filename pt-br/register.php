<?php

// Inclui o arquivo de configuração central
require_once 'config.php';

// Processar formulário
// register.php - Versão independente e funcional
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'cursinho');
define('DB_USER', 'root');
define('DB_PASS', '');

// Função de conexão com banco
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        // Se não conseguir conectar, usar modo simulação
        return null;
    }
}

// Função para sanitizar dados
function sanitize($data) {
    if (is_string($data)) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

// Função para verificar se está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// FUNÇÃO PROCESSREGISTER COMPLETA
function processRegister($data) {
    // Validações básicas
    if (empty($data['first_name']) || empty($data['last_name']) || 
        empty($data['username']) || empty($data['email']) || 
        empty($data['password']) || empty($data['confirm_password'])) {
        return ['success' => false, 'message' => 'Preencha todos os campos obrigatórios'];
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email inválido'];
    }
    
    if (strlen($data['password']) < 6) {
        return ['success' => false, 'message' => 'A senha deve ter no mínimo 6 caracteres'];
    }
    
    if ($data['password'] !== $data['confirm_password']) {
        return ['success' => false, 'message' => 'As senhas não coincidem'];
    }
    
    if (!isset($data['terms']) || $data['terms'] !== 'on') {
        return ['success' => false, 'message' => 'Você deve aceitar os termos de uso'];
    }
    
    // Tentar conectar com banco de dados
    $conn = getDBConnection();
    
    if ($conn) {
        // MODO REAL: Com banco de dados
        try {
            // Verificar se username já existe
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Nome de usuário já está em uso'];
            }
            
            // Verificar se email já existe
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email já está cadastrado'];
            }
            
            // Inserir novo usuário
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['username'],
                $data['email'],
                $password_hash
            ]);
            
            return ['success' => true, 'message' => 'Conta criada com sucesso! Faça login para continuar.'];
            
        } catch(PDOException $e) {
            return ['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()];
        }
    } else {
        // MODO SIMULAÇÃO: Sem banco de dados (para teste)
        // Simular registro bem-sucedido
        $_SESSION['user_id'] = rand(1000, 9999);
        $_SESSION['username'] = $data['username'];
        $_SESSION['first_name'] = $data['first_name'];
        $_SESSION['last_name'] = $data['last_name'];
        $_SESSION['is_admin'] = false;
        
        return ['success' => true, 'message' => 'Conta criada com sucesso! (Modo simulação)'];
    }
}

// Processar registro se formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = processRegister($_POST);


@@ -145,10 +31,9 @@ $title = 'Criar Conta';
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Incluindo os estilos principais do site -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - WebLearn</title>
    <!-- Incluindo os estilos principais do site -->
    <title><?php echo $title; ?> - WebLearn</title>
    
    <!-- Bootstrap CSS -->

@@ -158,47 +43,8 @@ $title = 'Criar Conta';
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="<?php echo getThemeClass(); ?> bg-gradient-body">
    <div class="container">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .required::after {
            content: " *";
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
<body class="<?php echo getThemeClass(); ?>">
    <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                
              $title = 'Criar Conta';
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus"></i> Criar Nova Conta
                        </h4>
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                    <div class="card-header">
                        <h1 class="h4 mb-0">
                            <i class="fas fa-user-plus" aria-hidden="true"></i> 
                            Criar Nova Conta

                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-user-plus"></i> Criar Conta
                            <button type="submit" class="btn btn-success w-100 mb-3">
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-user-plus" aria-hidden="true"></i> 
                                Criar Conta
                            </button>
                        </form>
                        
                        <hr>
                        
                        <div class="text-center">
                            <p class="mb-0">
                                Já tem uma conta? 
                                <a href="login.php" class="text-decoration-none">Faça login aqui</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scripts para mostrar/ocultar senhas e validação
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle para senha
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            
            // Toggle para confirmação de senha
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            // Função para alternar visibilidade da senha
            function togglePasswordVisibility(button, field) {
                if (button && field) {
                    const icon = button.querySelector('i');
                    
                    button.addEventListener('click', function() {
                        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                        field.setAttribute('type', type);
                        
                        if (type === 'password') {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                            button.setAttribute('aria-label', 'Mostrar senha');
                        } else {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                            button.setAttribute('aria-label', 'Ocultar senha');
                        }
                    });
                }
            }
            
            togglePasswordVisibility(togglePassword, passwordField);
            togglePasswordVisibility(toggleConfirmPassword, confirmPasswordField);
            
            // Validação de confirmação de senha
            function validatePasswordMatch() {
                if (confirmPasswordField && passwordField) {
                    if (confirmPasswordField.value && passwordField.value !== confirmPasswordField.value) {
                        confirmPasswordField.setCustomValidity('As senhas não coincidem');
                    } else {
                        confirmPasswordField.setCustomValidity('');
                    }
                }
            }
            
            if (passwordField) passwordField.addEventListener('input', validatePasswordMatch);
            if (confirmPasswordField) confirmPasswordField.addEventListener('input', validatePasswordMatch);
        });
    </script>
</body>
</html>

<?php 
// Limpar dados do formulário da sessão após exibição
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>