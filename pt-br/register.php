<?php
// register.php - Versão simplificada
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sessão
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexão com banco de dados
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=localhost;dbname=cursinho", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
}

// Funções básicas
function sanitize($data) {
    return is_string($data) ? htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8') : $data;
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

// Processamento do registro
function processRegister($data) {
    // Validações rápidas
    $required = ['first_name', 'last_name', 'username', 'email', 'password', 'confirm_password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => 'Preencha todos os campos obrigatórios'];
        }
    }
    
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Email inválido'];
    }
    
    if (strlen($data['password']) < 6) {
        return ['success' => false, 'message' => 'Senha deve ter no mínimo 6 caracteres'];
    }
    
    if ($data['password'] !== $data['confirm_password']) {
        return ['success' => false, 'message' => 'As senhas não coincidem'];
    }
    
    if (empty($data['terms'])) {
        return ['success' => false, 'message' => 'Aceite os termos de uso'];
    }

    $conn = getDBConnection();
    
    try {
        // Verificar usuário/email existente
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$data['username'], $data['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Usuário ou email já cadastrado'];
        }
        
        // Inserir novo usuário
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password_hash, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        
        return ['success' => true, 'message' => 'Conta criada com sucesso! Faça login.'];
        
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erro no sistema. Tente novamente.'];
    }
}

// Processar formulário
if ($_POST) {
    $result = processRegister($_POST);
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: login.php');
        exit;
    } else {
        $error = $result['message'];
        $form_data = $_POST;
    }
}

// Redirecionar se já estiver logado
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - WebLearn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }
        .btn-success:hover {
            transform: translateY(-2px);
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-success text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus"></i> Criar Nova Conta
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nome *</label>
                                        <input type="text" class="form-control" name="first_name" required
                                               value="<?php echo sanitize($form_data['first_name'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Sobrenome *</label>
                                        <input type="text" class="form-control" name="last_name" required
                                               value="<?php echo sanitize($form_data['last_name'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nome de Usuário *</label>
                                <input type="text" class="form-control" name="username" required
                                       pattern="[a-zA-Z0-9_]{3,}"
                                       value="<?php echo sanitize($form_data['username'] ?? ''); ?>">
                                <small class="form-text text-muted">Mínimo 3 caracteres (letras, números, _)</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required
                                       value="<?php echo sanitize($form_data['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Senha *</label>
                                        <input type="password" class="form-control" name="password" required minlength="6">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirmar Senha *</label>
                                        <input type="password" class="form-control" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="terms" required
                                       <?php echo isset($form_data['terms']) ? 'checked' : ''; ?>>
                                <label class="form-check-label">Aceito os termos de uso *</label>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 py-2">
                                <i class="fas fa-user-plus"></i> Criar Conta
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p class="mb-0">
                                Já tem conta? <a href="login.php" class="text-decoration-none">Fazer login</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação de senha em tempo real
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.querySelector('input[name="password"]');
            const confirmPassword = document.querySelector('input[name="confirm_password"]');
            
            function validatePasswords() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('As senhas não coincidem');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
            
            password.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
        });
    </script>
</body>
</html>