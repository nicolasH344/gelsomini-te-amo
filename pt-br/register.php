<?php
// Inclui o arquivo de configuração central
require_once 'config.php';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    <!-- Incluindo os estilos principais do site -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="<?php echo getThemeClass(); ?> bg-gradient-body">
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
                    <div class="card-header text-center">
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
                            
                            <button type="submit" class="btn btn-primary w-100 py-2">
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