<?php
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
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error'] = $result['message'];
        $_SESSION['form_data'] = $_POST; // Manter dados do formulário
    }
}

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Definir título da página
$title = 'Criar Conta';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - WebLearn</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                
                <!-- Mensagens de Alerta -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h1 class="h4 mb-0">
                            <i class="fas fa-user-plus" aria-hidden="true"></i> 
                            Criar Nova Conta
                        </h1>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="register.php" novalidate>
                            <fieldset>
                                <legend class="visually-hidden">Informações pessoais</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label required">Nome</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="first_name" 
                                                   name="first_name" 
                                                   required
                                                   autocomplete="given-name"
                                                   aria-describedby="first_name-help"
                                                   value="<?php echo isset($_SESSION['form_data']['first_name']) ? sanitize($_SESSION['form_data']['first_name']) : ''; ?>">
                                            <div id="first_name-help" class="form-text">
                                                Digite seu primeiro nome
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label required">Sobrenome</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="last_name" 
                                                   name="last_name" 
                                                   required
                                                   autocomplete="family-name"
                                                   aria-describedby="last_name-help"
                                                   value="<?php echo isset($_SESSION['form_data']['last_name']) ? sanitize($_SESSION['form_data']['last_name']) : ''; ?>">
                                            <div id="last_name-help" class="form-text">
                                                Digite seu sobrenome
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <fieldset>
                                <legend class="visually-hidden">Informações de acesso</legend>
                                <div class="mb-3">
                                    <label for="username" class="form-label required">Nome de Usuário</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           required
                                           autocomplete="username"
                                           aria-describedby="username-help"
                                           pattern="[a-zA-Z0-9_]{3,}"
                                           value="<?php echo isset($_SESSION['form_data']['username']) ? sanitize($_SESSION['form_data']['username']) : ''; ?>">
                                    <div id="username-help" class="form-text">
                                        Apenas letras, números e underscore. Mínimo 3 caracteres.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label required">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           required
                                           autocomplete="email"
                                           aria-describedby="email-help"
                                           value="<?php echo isset($_SESSION['form_data']['email']) ? sanitize($_SESSION['form_data']['email']) : ''; ?>">
                                    <div id="email-help" class="form-text">
                                        Digite um endereço de email válido
                                    </div>
                                </div>
                            </fieldset>
                            
                            <fieldset>
                                <legend class="visually-hidden">Definir senha</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label required">Senha</label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="password" 
                                                       name="password" 
                                                       required
                                                       autocomplete="new-password"
                                                       aria-describedby="password-help"
                                                       minlength="6">
                                                <button class="btn btn-outline-secondary" 
                                                        type="button" 
                                                        id="togglePassword"
                                                        aria-label="Mostrar/ocultar senha">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div id="password-help" class="form-text">
                                                Mínimo 6 caracteres. Use uma combinação de letras, números e símbolos.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label required">Confirmar Senha</label>
                                            <div class="input-group">
                                                <input type="password" 
                                                       class="form-control" 
                                                       id="confirm_password" 
                                                       name="confirm_password" 
                                                       required
                                                       autocomplete="new-password"
                                                       aria-describedby="confirm_password-help">
                                                <button class="btn btn-outline-secondary" 
                                                        type="button" 
                                                        id="toggleConfirmPassword"
                                                        aria-label="Mostrar/ocultar confirmação de senha">
                                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div id="confirm_password-help" class="form-text">
                                                Digite a mesma senha novamente
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="terms" 
                                       name="terms"
                                       required
                                       aria-describedby="terms-help"
                                       <?php echo (isset($_SESSION['form_data']['terms']) && $_SESSION['form_data']['terms'] === 'on') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="terms">
                                    Concordo com os termos de uso e política de privacidade
                                </label>
                                <div id="terms-help" class="form-text">
                                    É necessário aceitar os termos para criar uma conta
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100 mb-3">
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