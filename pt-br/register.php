<?php 
// Incluir configurações
require_once 'config.php';

// Variável para armazenar a mensagem de erro localmente
$error = null;

// Processar registro se formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = processRegister($_POST);
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
        header('Location: login.php');
        exit;
    } else {
        // Usar uma variável local para exibir o erro na página
        $error = $result['message'];
    }
}

// Se já estiver logado, redirecionar
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Definir título da página
$title = 'Criar Conta';

include 'header.php'; 
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            
            <?php // Exibir a mensagem de erro local, se houver ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                    <?php echo sanitize($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
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
                            <legend class="visually-hidden">Informação Pessoal</legend>
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
                                               value="<?php echo isset($_POST['first_name']) ? sanitize($_POST['first_name']) : ''; ?>">
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
                                               value="<?php echo isset($_POST['last_name']) ? sanitize($_POST['last_name']) : ''; ?>">
                                        <div id="last_name-help" class="form-text">
                                            Digite seu sobrenome
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset>
                            <legend class="visually-hidden">Informação de Acesso</legend>
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
                                       value="<?php echo isset($_POST['username']) ? sanitize($_POST['username']) : ''; ?>">
                                <div id="username-help" class="form-text">
                                    Apenas letras, números e underline. Mínimo 3 caracteres.
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
                                       value="<?php echo isset($_POST['email']) ? sanitize($_POST['email']) : ''; ?>">
                                <div id="email-help" class="form-text">
                                    Digite um endereço de email válido
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset>
                            <legend class="visually-hidden">Definir Senha</legend>
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
                                            Mínimo 6 caracteres
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
                                   <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
                            <label class="form-check-label required" for="terms">
                                Aceito os <a href="terms.php" class="text-decoration-none">termos de uso</a> e <a href="privacy.php" class="text-decoration-none">política de privacidade</a>
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

<?php include 'footer.php'; ?>